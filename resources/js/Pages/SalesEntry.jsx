import React, { useState, useEffect, useMemo, useRef } from "react";
import Select from "react-select";

const CustomerList = React.memo(({ customers, type, searchQuery, onSearchChange, selectedPrintedCustomer, selectedUnprintedCustomer, handleCustomerClick, unprintedTotal, formatDecimal }) => (
  <div className="w-1/5 bg-white shadow-xl rounded-xl p-4 overflow-y-auto max-h-screen">
    <h2 className="text-xl font-bold mb-4">{type === 'printed' ? 'Printed Customers' : 'Unprinted Sales'}</h2>
    {type === 'unprinted' && (
      <div className="bg-gray-50 p-3 rounded-xl shadow-sm mb-4">
        <h3 className="text-lg font-semibold text-gray-800">Total Unprinted: <span className="text-red-600 font-bold">Rs. {formatDecimal(unprintedTotal)}</span></h3>
      </div>
    )}
    <div className="mb-4">
      <input 
        type="text" 
        placeholder={`Search by ${type === 'printed' ? 'Bill No or Code...' : 'Customer Code...'}`} 
        value={searchQuery} 
        onChange={e => onSearchChange(e.target.value)} 
        className="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300" 
      />
    </div>
    {customers.length === 0 ? <p className="text-gray-500">No {type === 'printed' ? 'printed sales' : 'unprinted sales'} found.</p> : (
      <ul>{customers.map(customerCode => (
        <li key={customerCode}>
          <button 
            onClick={() => handleCustomerClick(type, customerCode)} 
            className={`w-full text-left p-3 mb-2 rounded-xl border ${(type === 'printed' ? selectedPrintedCustomer : selectedUnprintedCustomer) === customerCode ? "bg-blue-500 text-white border-blue-600" : "bg-gray-50 hover:bg-gray-100 border-gray-200"}`}
          >
            <div className="font-medium">{customerCode}</div>
          </button>
        </li>
      ))}</ul>
    )}
  </div>
));

export default function SalesEntry() {
  // Initial data
  const initialData = {
    sales: (window.__INITIAL_SALES__ || []).filter(s => s.id),
    printed: (window.__PRINTED_SALES__ || []).filter(s => s.id),
    unprinted: (window.__UNPRINTED_SALES__ || []).filter(s => s.id),
    customers: window.__CUSTOMERS__ || [],
    entries: window.__ENTRIES__ || [],
    storeUrl: window.__STORE_URL__ || "/grn",
    csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
    routes: window.__ROUTES__ || {
      markPrinted: '/sales/mark-printed',
      getLoanAmount: '/get-loan-amount',
      markAllProcessed: '/sales/mark-all-processed'
    }
  };

  // Refs
  const refs = {
    customerCode: useRef(null), customerSelect: useRef(null), givenAmount: useRef(null),
    grnSelect: useRef(null), itemName: useRef(null), weight: useRef(null),
    packs: useRef(null), pricePerKg: useRef(null), total: useRef(null)
  };

 const fieldOrder = ["customer_code_input", "customer_code_select", "given_amount", "grn_entry_code", "item_name", "weight", "packs", "price_per_kg", "total"];
 const skipMap = { 
  customer_code_input: "grn_entry_code", 
  grn_entry_code: "weight" 
};

  // State
  const [allSales, setAllSales] = useState([...initialData.sales, ...initialData.printed, ...initialData.unprinted]);
  const [selectedPrintedCustomer, setSelectedPrintedCustomer] = useState(null);
  const [selectedUnprintedCustomer, setSelectedUnprintedCustomer] = useState(null);
  const [editingSaleId, setEditingSaleId] = useState(null);
  const [grnSearchInput, setGrnSearchInput] = useState("");
  const [searchQueries, setSearchQueries] = useState({ printed: "", unprinted: "" });
  const [errors, setErrors] = useState({});
  const [balanceInfo, setBalanceInfo] = useState({ balancePacks: 0, balanceWeight: 0 });
  const [loanAmount, setLoanAmount] = useState(0);
  const [form, setForm] = useState({
    customer_code: "", customer_name: "", supplier_code: "", code: "", item_code: "",
    item_name: "", weight: "", price_per_kg: "", total: "", packs: "", grn_entry_code: "",
    original_weight: "", original_packs: "", given_amount: ""
  });

  // Derived data
  const { newSales, printedSales, unprintedSales } = useMemo(() => ({
    newSales: allSales.filter(s => s.id && s.bill_printed !== 'Y' && s.bill_printed !== 'N'),
    printedSales: allSales.filter(s => s.bill_printed === 'Y'),
    unprintedSales: allSales.filter(s => s.bill_printed === 'N')
  }), [allSales]);

  const filterCustomers = (sales, query, searchByBillNo = false) => {
    const allCustomers = [...new Set(sales.map(s => s.customer_code))];
    if (!query) return allCustomers;
    const lowerQuery = query.toLowerCase();
    if (searchByBillNo) {
      const byBillNo = sales.filter(s => (s.bill_no?.toString() || '').toLowerCase().includes(lowerQuery)).map(s => s.customer_code);
      const byCode = allCustomers.filter(code => code.toLowerCase().includes(lowerQuery));
      return [...new Set([...byBillNo, ...byCode])];
    }
    return allCustomers.filter(code => code.toLowerCase().includes(lowerQuery));
  };

  const printedCustomers = useMemo(() => filterCustomers(printedSales, searchQueries.printed, true), [printedSales, searchQueries.printed]);
  const unprintedCustomers = useMemo(() => filterCustomers(unprintedSales, searchQueries.unprinted), [unprintedSales, searchQueries.unprinted]);

  const displayedSales = useMemo(() => {
    let sales = newSales;
    if (selectedUnprintedCustomer) sales = [...sales, ...unprintedSales.filter(s => s.customer_code === selectedUnprintedCustomer)];
    else if (selectedPrintedCustomer) sales = [...sales, ...printedSales.filter(s => s.customer_code === selectedPrintedCustomer)];
    return sales;
  }, [newSales, unprintedSales, printedSales, selectedUnprintedCustomer, selectedPrintedCustomer]);

  const currentBillNo = useMemo(() => selectedPrintedCustomer ? printedSales.find(s => s.customer_code === selectedPrintedCustomer)?.bill_no || "N/A" : "", [selectedPrintedCustomer, printedSales]);
  const calculateTotal = (sales) => sales.reduce((acc, s) => acc + (parseFloat(s.total) || parseFloat(s.weight || 0) * parseFloat(s.price_per_kg || 0) || 0), 0);
  const mainTotal = calculateTotal(displayedSales);
  const unprintedTotal = calculateTotal(unprintedSales);
  const formatDecimal = (val) => (Number.isFinite(parseFloat(val)) ? parseFloat(val).toFixed(2) : "0.00");

  // Effects
  useEffect(() => {
    if (form.grn_entry_code) {
      const matchingEntry = initialData.entries.find((en) => en.code === form.grn_entry_code);
      setBalanceInfo(matchingEntry ? { balancePacks: matchingEntry.packs || 0, balanceWeight: matchingEntry.weight || 0 } : { balancePacks: 0, balanceWeight: 0 });
    } else {
      setBalanceInfo({ balancePacks: 0, balanceWeight: 0 });
    }
  }, [form.grn_entry_code, initialData.entries]);

  useEffect(() => {
    const w = parseFloat(form.weight) || 0;
    const p = parseFloat(form.price_per_kg) || 0;
    setForm(prev => ({ ...prev, total: w * p ? Number((w * p).toFixed(2)) : "" }));
  }, [form.weight, form.price_per_kg]);

  useEffect(() => { refs.customerCode.current?.focus(); }, []);

  // API functions
  const apiCall = async (url, method, body) => {
    try {
      const res = await fetch(url, {
        method,
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": initialData.csrf,
          ...(method !== "DELETE" && { "Accept": "application/json" })
        },
        body: body ? JSON.stringify(body) : undefined
      });
      const data = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(data.error || "Server error: " + res.statusText);
      return data;
    } catch (error) { throw error; }
  };

  const fetchLoanAmount = async (customerCode) => {
    if (!customerCode) return setLoanAmount(0);
    try {
      const loanResponse = await fetch(initialData.routes.getLoanAmount, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': initialData.csrf },
        body: JSON.stringify({ customer_short_name: customerCode })
      });
      const loanData = await loanResponse.json();
      setLoanAmount(parseFloat(loanData.total_loan_amount) || 0);
    } catch (loanError) {
      console.error('Error fetching loan amount:', loanError);
      setLoanAmount(0);
    }
  };

  // Event handlers
  const handleKeyDown = (e, currentFieldIndex) => {
  if (e.key === "Enter") {
    e.preventDefault();
    
    // Handle given_amount submission
    if (fieldOrder[currentFieldIndex] === "given_amount" && form.given_amount) {
      return handleSubmitGivenAmount(e);
    }
    
    // Handle price_per_kg submission - only submit if we're actually in price_per_kg field
    if (fieldOrder[currentFieldIndex] === "price_per_kg") {
      return handleSubmit(e);
    }

    // For all other fields, move to next field
    let nextIndex = currentFieldIndex + 1;
    if (skipMap[fieldOrder[currentFieldIndex]]) {
      const targetIndex = fieldOrder.findIndex(f => f === skipMap[fieldOrder[currentFieldIndex]]);
      if (targetIndex !== -1) nextIndex = targetIndex;
    }
    
    requestAnimationFrame(() => setTimeout(() => {
      const nextRef = Object.values(refs)[nextIndex];
      if (nextRef?.current) {
        if (nextRef.current.focus) {
          nextRef.current.focus();
        } else if (nextRef.current.select) {
          nextRef.current.select.focus();
        }
      }
    }, 0));
  }
};

  const handleInputChange = (e, fieldIndex = null) => {
    const { name, value } = e.target;
    setForm(prev => ({ ...prev, [name]: value }));

    if (name === 'customer_code') {
      const trimmedValue = value.trim();
      const matchingCustomer = unprintedCustomers.find(code => code.toLowerCase() === trimmedValue.toLowerCase());
      if (matchingCustomer) {
        setSelectedUnprintedCustomer(matchingCustomer);
        setSelectedPrintedCustomer(null);
      } else if (selectedUnprintedCustomer) setSelectedUnprintedCustomer(null);

      if (value.length === 10 && fieldIndex !== null && fieldIndex + 1 < fieldOrder.length) {
        let nextIndex = fieldIndex + 1;
        if (skipMap[fieldOrder[fieldIndex + 1]]) {
          const targetIndex = fieldOrder.findIndex(f => f === skipMap[fieldOrder[fieldIndex + 1]]);
          if (targetIndex !== -1) nextIndex = targetIndex;
        }

        requestAnimationFrame(() => setTimeout(() => {
          const nextRef = Object.values(refs)[nextIndex];
          if (nextRef?.current) nextRef.current.focus?.() || nextRef.current.select?.();
        }, 0));
      }
    }
  };

  const handleCustomerSelect = (e) => {
    const short = e.target.value;
    const customer = initialData.customers.find(x => String(x.short_name) === String(short));
    const hasUnprintedSales = unprintedCustomers.includes(short);

    setSelectedUnprintedCustomer(hasUnprintedSales ? short : null);
    setSelectedPrintedCustomer(null);
    setForm(prev => ({ ...prev, customer_code: short || prev.customer_code, customer_name: customer?.name || "" }));
    fetchLoanAmount(short);
  };

  const handleEditClick = (sale) => {
    setForm({
      ...sale,
      grn_entry_code: sale.grn_entry_code || sale.code || "",
      item_name: sale.item_name || "",
      customer_code: sale.customer_code || "",
      customer_name: sale.customer_name || "",
      supplier_code: sale.supplier_code || "",
      item_code: sale.item_code || "",
      weight: sale.weight || "",
      price_per_kg: sale.price_per_kg || "",
      total: sale.total || "",
      packs: sale.packs || "",
      original_weight: sale.original_weight || "",
      original_packs: sale.original_packs || "",
    });
    setEditingSaleId(sale.id);

    setTimeout(() => {
      refs.weight.current?.focus();
      refs.weight.current?.select();
    }, 0);
  };

  const handleTableRowKeyDown = (e, sale) => {
    if (e.key === "Enter") {
      e.preventDefault();
      handleEditClick(sale);
    }
  };

  const handleClearForm = () => {
    setForm({
      customer_code: "", customer_name: "", supplier_code: "", code: "", item_code: "",
      item_name: "", weight: "", price_per_kg: "", total: "", packs: "", grn_entry_code: "",
      original_weight: "", original_packs: "", given_amount: ""
    });
    setEditingSaleId(null);
    setGrnSearchInput("");
    setBalanceInfo({ balancePacks: 0, balanceWeight: 0 });
    setLoanAmount(0);
  };

  const handleDeleteClick = async () => {
    if (!editingSaleId || !window.confirm("Are you sure you want to delete this sales record?")) return;
    try {
      await apiCall(`/sales/${editingSaleId}`, "DELETE");
      setAllSales(prev => prev.filter(s => s.id !== editingSaleId));
      handleClearForm();
      alert("Record deleted successfully.");
    } catch (error) { setErrors({ form: error.message }); }
  };

  const handleSubmitGivenAmount = async (e) => {
    e.preventDefault();
    setErrors({});

    if (!form.customer_code) {
      setErrors({ form: "Please enter a customer code first" });
      refs.customerCode.current?.focus();
      return;
    }

    if (!form.given_amount) {
      setErrors({ form: "Please enter a given amount" });
      return;
    }

    const customerSales = allSales.filter(s => s.customer_code === form.customer_code);
    const firstSale = customerSales[0];
    if (!firstSale) {
      setErrors({ form: "No sales records found for this customer. Please add a sales record first." });
      return;
    }

    try {
      const data = await apiCall(`/sales/${firstSale.id}/given-amount`, "PUT", { given_amount: parseFloat(form.given_amount) || 0 });
      setAllSales(prev => prev.map(s => s.id === data.sale.id ? data.sale : s));
      setForm(prev => ({ ...prev, given_amount: "" }));
      refs.grnSelect.current?.focus();
      alert("Given amount updated successfully for customer: " + form.customer_code);
    } catch (error) {
      setErrors({ form: error.message });
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrors({});
    const isEditing = editingSaleId !== null;
    let billPrintedStatus = undefined;

    if (!isEditing) {
      if (selectedPrintedCustomer) billPrintedStatus = 'Y';
      else if (selectedUnprintedCustomer) billPrintedStatus = 'N';
    }

    const customerSales = allSales.filter(s => s.customer_code === form.customer_code);
    const isFirstRecordForCustomer = customerSales.length === 0 && !isEditing;

    const payload = {
      supplier_code: form.supplier_code,
      customer_code: (form.customer_code || "").toUpperCase(),
      customer_name: form.customer_name,
      code: form.code || form.grn_entry_code,
      item_code: form.item_code,
      item_name: form.item_name,
      weight: parseFloat(form.weight) || 0,
      price_per_kg: parseFloat(form.price_per_kg) || 0,
      total: parseFloat(form.total) || 0,
      packs: parseInt(form.packs) || 0,
      grn_entry_code: form.grn_entry_code,
      original_weight: form.original_weight,
      original_packs: form.original_packs,
      given_amount: (isFirstRecordForCustomer || (isEditing && customerSales[0]?.id === editingSaleId))
        ? (form.given_amount ? parseFloat(form.given_amount) : null)
        : null,
      ...(billPrintedStatus && { bill_printed: billPrintedStatus }),
    };

    try {
      const url = isEditing ? `/sales/${editingSaleId}` : initialData.storeUrl;
      const method = isEditing ? "PUT" : "POST";
      const data = await apiCall(url, method, payload);

      let newSale = isEditing ? data.sale : data.data || {};
      if (!isEditing && billPrintedStatus && !newSale.bill_printed) newSale = { ...newSale, bill_printed: billPrintedStatus };

      setAllSales(prev => isEditing ? prev.map(s => s.id === newSale.id ? newSale : s) : [...prev, newSale]);
      
      setForm(prevForm => ({
        customer_code: prevForm.customer_code,
        customer_name: prevForm.customer_name,
        supplier_code: "", code: "", item_code: "", item_name: "", weight: "", price_per_kg: "", total: "", packs: "", 
        grn_entry_code: "", original_weight: "", original_packs: "", given_amount: ""
      }));

      setEditingSaleId(null);
      setGrnSearchInput("");
      setBalanceInfo({ balancePacks: 0, balanceWeight: 0 });
      refs.grnSelect.current?.focus();
    } catch (error) {
      setErrors({ form: error.message });
    }
  };

  const handleCustomerClick = (type, customerCode) => {
    const isPrinted = type === 'printed';
    const isCurrentlySelected = isPrinted ? selectedPrintedCustomer === customerCode : selectedUnprintedCustomer === customerCode;

    if (isPrinted) {
      setSelectedPrintedCustomer(isCurrentlySelected ? null : customerCode);
      setSelectedUnprintedCustomer(null);
    } else {
      setSelectedUnprintedCustomer(isCurrentlySelected ? null : customerCode);
      setSelectedPrintedCustomer(null);
    }

    const customer = initialData.customers.find(x => String(x.short_name) === String(customerCode));
    const customerSale = allSales.find(s => s.customer_code === customerCode);
    const newCustomerCode = isCurrentlySelected ? "" : customerCode;
    
    setForm(prev => ({
      ...prev,
      customer_code: newCustomerCode,
      customer_name: isCurrentlySelected ? "" : customer?.name || "",
      given_amount: isCurrentlySelected ? "" : (customerSale?.given_amount || "") 
    }));
    
    fetchLoanAmount(newCustomerCode);
    refs.grnSelect.current?.focus();
  };

  // Receipt functions
  const buildFullReceiptHTML = (salesData, billNo, customerName, mobile, globalLoanAmount = 0) => {
    const date = new Date().toLocaleDateString();
    const time = new Date().toLocaleTimeString();
    let totalAmountSum = 0, totalPacksSum = 0;
    const itemGroups = {};

    const itemsHtml = salesData.map(s => {
      totalAmountSum += parseFloat(s.total) || 0;
      const packs = parseInt(s.packs) || 0;
      totalPacksSum += packs;

      if (!itemGroups[s.item_name]) itemGroups[s.item_name] = { totalWeight: 0, totalPacks: 0 };
      itemGroups[s.item_name].totalWeight += parseFloat(s.weight) || 0;
      itemGroups[s.item_name].totalPacks += packs;

      return `<tr style="font-size:1.2em;">
        <td style="text-align:left;">${s.item_name || ""} <br>${packs}</td>
        <td style="text-align:right; padding-right:18px;">${(parseFloat(s.weight) || 0).toFixed(2)}</td>
        <td style="text-align:right;">${(parseFloat(s.price_per_kg) || 0).toFixed(2)}</td>
        <td style="text-align:right;">${(parseFloat(s.total) || 0).toFixed(2)}</td>
      </tr>`;
    }).join("");

    const packCostTotal = window.globalTotalPackCostValue || 0;
    const totalPrice = totalAmountSum;
    const givenAmount = salesData.reduce((sum, s) => sum + (parseFloat(s.given_amount) || 0), 0);
    const remaining = givenAmount - (totalPrice + packCostTotal);

    let itemSummaryHtml = '';
    const entries = Object.entries(itemGroups);
    for (let i = 0; i < entries.length; i += 2) {
      const first = entries[i], second = entries[i + 1];
      itemSummaryHtml += '<div style="display:flex; gap:0.5rem; margin-bottom:0.2rem;">';
      itemSummaryHtml += `<span style="padding:0.1rem 0.3rem;border-radius:0.5rem;background-color:#f3f4f6;font-size:0.6rem;">
        <strong>${first[0]}</strong>:${first[1].totalWeight.toFixed(2)}/${first[1].totalPacks}</span>`;
      if (second) itemSummaryHtml += `<span style="padding:0.1rem 0.3rem;border-radius:0.5rem;background-color:#f3f4f6;font-size:0.6rem;">
        <strong>${second[0]}</strong>:${second[1].totalWeight.toFixed(2)}/${second[1].totalPacks}</span>`;
      itemSummaryHtml += '</div>';
    }

    const givenAmountRow = givenAmount > 0 ? `<tr>
      <td style="width:50%;text-align:left;white-space:nowrap;"><span style="font-size:0.75rem;">දුන් මුදල: </span><span style="font-weight:bold;font-size:0.9rem;">${givenAmount.toFixed(2)}</span></td>
      <td style="width:50%;text-align:right;white-space:nowrap;font-size:1rem;"><span style="font-size:0.8rem;">ඉතිරිය: </span><span style="font-weight:bold;font-size:1.5rem;">${Math.abs(remaining).toFixed(2)}</span></td>
    </tr>` : '';

    const loanRow = globalLoanAmount > 0 ? `<tr>
      <td style="font-weight:normal;font-size:0.9rem;text-align:left;">පෙර ණය: Rs. <span>${globalLoanAmount.toFixed(2)}</span></td>
      <td style="font-weight:bold;text-align:right;font-size:1.5em;">Rs. ${(globalLoanAmount + totalPrice + packCostTotal).toFixed(2)}</td>
    </tr>` : '';

    return `<div class="receipt-container" style="width:100%;max-width:300px;margin:0 auto;padding:5px;">
      <div style="text-align:center;margin-bottom:5px;">
        <h3 style="font-size:1.8em;font-weight:bold;margin:0;"><span style="border:2px solid #000;padding:0.1em 0.3em;display:inline-block;margin-right:5px;">B32</span>TAG ට්‍රේඩර්ස්</h3>
        <p style="margin:0;font-size:0.7em;">අල, ෆී ළූනු, කුළුබඩු තොග ගෙන්වන්නෝ බෙදාහරින්නෝ</p>
        <p style="margin:0;font-size:0.7em;">වි.ආ.ම. වේයන්ගොඩ</p>
      </div>
      <div style="text-align:left;margin-bottom:5px;">
        <table style="width:100%;font-size:9px;border-collapse:collapse;">
          <tr><td style="width:50%;">දිනය : ${date}</td><td style="width:50%;text-align:right;">${time}</td></tr>
          <tr><td colspan="2">දුර : ${mobile || ''}</td></tr>
          <tr><td>බිල් අංකය : <strong>${billNo}</strong></td><td style="text-align:right;"><strong style="font-size:1.8em;">${customerName.toUpperCase()}</strong></td></tr>
        </table>
      </div>
      <hr style="border:0.5px solid #000;margin:5px 0;">
      <table style="width:100%;font-size:9px;border-collapse:collapse;">
        <thead style="font-size:1.5em;">
          <tr><th style="text-align:left;padding:2px;">වර්ගය<br>මලු</th><th style="padding:2px;">කිලෝ</th><th style="padding:2px;">මිල</th><th style="text-align:right;padding:2px;">අගය</th></tr>
        </thead>
        <tbody>
          <tr><td colspan="4"><hr style="height:1px;background-color:#000;margin:2px 0;"></td></tr>
          ${itemsHtml}
          <tr><td colspan="4"><hr style="border:0.5px solid #000;margin:5px 0;"></td></tr>
          <tr><td colspan="2" style="text-align:left;font-weight:bold;font-size:1.2em;">${totalPacksSum}</td><td colspan="2" style="text-align:right;font-weight:bold;font-size:1.2em;">${totalPrice.toFixed(2)}</td></tr>
        </tbody>
      </table>
      <table style="width:100%;font-size:11px;border-collapse:collapse;">
        <tr><td>ප්‍රවාහන ගාස්තු:</td><td style="text-align:right;font-weight:bold;">00</td></tr>
        <tr><td>කුලිය:</td><td style="text-align:right;font-weight:bold;">${packCostTotal.toFixed(2)}</td></tr>
        <tr><td>අගය:</td><td style="text-align:right;font-weight:bold;"><span style="display:inline-block;border-top:1px solid #000;border-bottom:3px double #000;padding:2px 4px;min-width:80px;text-align:right;">${(totalPrice + packCostTotal).toFixed(2)}</span></td></tr>
        ${givenAmountRow}${loanRow}
      </table>
      <hr style="border:0.5px solid #000;margin:5px 0;">
      <div style="font-size:10px;">${itemSummaryHtml}</div>
      <div style="text-align:center;margin-top:10px;font-size:10px;">
        <p style="margin:0;">භාණ්ඩ පරීක්ෂාකර බලා රැගෙන යන්න</p><p style="margin:0;">නැවත භාර ගනු නොලැබේ</p>
      </div>
    </div>`;
  };

  const printReceipt = (html, customerName) => new Promise((resolve) => {
    const printWindow = window.open("", "_blank");
    printWindow.document.write(`<!DOCTYPE html><html><head><title>${customerName}</title></head><body>${html}</body></html>`);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    requestAnimationFrame(() => setTimeout(() => { printWindow.close(); resolve(); }, 500));
  });

  const handlePrintAndClear = async () => {
    const salesData = displayedSales.filter(s => s.id);
    if (!salesData.length) return alert("No sales records to print!");

    try {
      const data = await apiCall(initialData.routes.markPrinted, "POST", { sales_ids: salesData.map(s => s.id) });
      if (data.status !== "success") throw new Error(data.message || "Unknown error");

      const customerCode = salesData[0].customer_code || "N/A";
      const customerName = customerCode;
      const mobile = salesData[0].mobile || '0773358518';
      const billNo = data.bill_no || "";

      let globalLoanAmount = 0;
      try {
        const loanResponse = await fetch(initialData.routes.getLoanAmount, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': initialData.csrf },
          body: JSON.stringify({ customer_short_name: customerCode })
        });
        const loanData = await loanResponse.json();
        globalLoanAmount = parseFloat(loanData.total_loan_amount) || 0;
      } catch (loanError) {
        console.error('Error fetching loan amount:', loanError);
      }

      const receiptHtml = buildFullReceiptHTML(salesData, billNo, customerName, mobile, globalLoanAmount);
      await printReceipt(receiptHtml, customerName);
      await printReceipt(`<div style="text-align:center;font-size:2em;font-weight:bold;color:red;margin-bottom:10px;">COPY</div>${receiptHtml}`, customerName + " - Copy");

      setAllSales(prev => prev.map(s => salesData.map(d => d.id).includes(s.id) ? { ...s, bill_printed: 'Y', bill_no: billNo } : s));
      setSelectedUnprintedCustomer(null);
      setSelectedPrintedCustomer(null);
      handleClearForm();
    } catch (error) { 
      alert("Printing failed: " + error.message); 
    }
  };

  // Shortcut effects
  useEffect(() => {
    const handleShortcut = (e) => {
      if (e.key === "F1") {
        e.preventDefault();
        handlePrintAndClear().finally(() => {
          const focusCustomerCode = () => refs.customerCode.current?.focus();
          [100, 200, 300, 500, 800].forEach(timeout => setTimeout(focusCustomerCode, timeout));
        });
      } else if (e.key === "F5") {
        e.preventDefault();
        const salesToProcess = [...newSales, ...unprintedSales];
        if (salesToProcess.length === 0) return alert("No sales to process.");

        if (window.confirm(`Are you sure you want to mark ALL sales as processed?`)) {
          apiCall(initialData.routes.markAllProcessed, "POST", { sales_ids: salesToProcess.map(s => s.id) })
            .then(data => {
              if (data.success) {
                alert(data.message || `All ${salesToProcess.length} sales marked as processed successfully!`);
                setAllSales(prev => prev.map(s => salesToProcess.map(ps => ps.id).includes(s.id) ? { ...s, bill_printed: "N" } : s));
                handleClearForm();
                setSelectedUnprintedCustomer(null);
                setSelectedPrintedCustomer(null);
                const lockFocus = () => refs.customerCode.current?.focus();
                [100, 200, 300, 400, 500].forEach(timeout => setTimeout(lockFocus, timeout));
              } else alert(data.message || "Failed to mark sales as processed.");
            })
            .catch(err => alert("Failed to mark sales as processed: " + err.message));
        }
      }
    };
    window.addEventListener("keydown", handleShortcut);
    return () => window.removeEventListener("keydown", handleShortcut);
  }, [displayedSales, newSales]);

  // Main render
  return (
    <div className="min-h-screen flex flex-row bg-gray-100 p-6">
      <CustomerList customers={printedCustomers} type="printed" searchQuery={searchQueries.printed} 
        onSearchChange={(value) => setSearchQueries(prev => ({ ...prev, printed: value }))} 
        selectedPrintedCustomer={selectedPrintedCustomer} selectedUnprintedCustomer={selectedUnprintedCustomer}
        handleCustomerClick={handleCustomerClick} unprintedTotal={unprintedTotal} formatDecimal={formatDecimal} />

      <div className="w-3/5 bg-white shadow-2xl rounded-3xl p-10 mx-6">
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="flex justify-between items-center bg-gray-50 p-4 rounded-xl shadow-sm">
            <span className="text-gray-600 font-medium">Bill No: {currentBillNo}</span>
            <h2 className="text-2xl font-bold text-red-600">Total Sales: Rs. {formatDecimal(mainTotal)}</h2>
          </div>

          <div className="grid grid-cols-1 gap-4">
            <div className="grid grid-cols-3 gap-4">
              <input id="customer_code_input" ref={refs.customerCode} name="customer_code" value={form.customer_code} 
                onChange={(e) => handleInputChange(e, 0)} onKeyDown={(e) => handleKeyDown(e, 0)} type="text" maxLength={10} 
                placeholder="Customer Code" className="px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300" />
              
              <select id="customer_code_select" ref={refs.customerSelect} value={form.customer_code} 
                onChange={handleCustomerSelect} onKeyDown={(e) => handleKeyDown(e, 1)} className="px-4 py-2 border rounded-xl">
                <option value="">-- Select Customer --</option>
                {initialData.customers.map(c => <option key={c.short_name} value={c.short_name}>{c.name} ({c.short_name})</option>)}
              </select>

              <input type="text" readOnly value={`Loan: Rs. ${formatDecimal(loanAmount)}`} placeholder="Loan Amount" 
                className="px-4 py-2 border rounded-xl bg-yellow-100 text-red-600 font-bold" />
            </div>

            <Select
              id="grn_entry_code"
              ref={refs.grnSelect}
              value={form.grn_entry_code ? { value: form.grn_entry_code, label: form.grn_entry_code, 
                data: initialData.entries.find((en) => en.code === form.grn_entry_code) } : null}
              onChange={(selected) => {
                if (selected?.data) {
                  const entry = selected.data;
                  setForm(prev => ({
                    ...prev,
                    grn_entry_code: selected.value,
                    item_name: entry.item_name || "",
                    supplier_code: entry.supplier_code || "",
                    item_code: entry.item_code || "",
                    price_per_kg: entry.price_per_kg || entry.PerKGPrice || entry.SalesKGPrice || "",
                    weight: editingSaleId ? prev.weight : "",
                    packs: editingSaleId ? prev.packs : "",
                    total: editingSaleId ? prev.total : ""
                  }));
                  setGrnSearchInput("");
                  requestAnimationFrame(() => setTimeout(() => refs.weight.current?.focus(), 10));
                }
              }}
              onInputChange={setGrnSearchInput}
              onKeyDown={(e) => {
                if (e.key === "Enter" && form.grn_entry_code && !e.isPropagationStopped()) {
                  e.preventDefault();
                  setTimeout(() => refs.weight.current?.focus(), 0);
                }
              }}
              getOptionLabel={(option) => `${option.data?.code} - ${option.data?.item_name || "Unknown Item"}`}
              getOptionValue={(option) => option.value}
              options={initialData.entries.map((en, index) => ({ value: en.code, label: en.code, data: en, index }))}
              placeholder="Select GRN Entry"
              isSearchable={true}
              noOptionsMessage={() => "No GRN entries found"}
              formatOptionLabel={(option, { context }) => {
                if (context === "value" || !option.data) {
                  const entry = option.data || initialData.entries.find((en) => en.code === option.value);
                  return <span>{option.label}(<strong>Price:</strong> Rs.{formatDecimal(entry?.price_per_kg || entry?.PerKGPrice || entry?.SalesKGPrice)} / <strong>BW:</strong> {formatDecimal(entry?.weight)} / <strong>BP:</strong> {entry?.packs || 0})</span>;
                }
                const entry = option.data;
                return <div className="w-full">
                  {option.index === 0 && <div className="grid grid-cols-6 gap-1 px-3 py-2 bg-gray-100 font-bold text-xs border-b border-gray-300">
                    <div className="text-left">Code</div><div className="text-center">OP</div><div className="text-center">OW</div><div className="text-center">BP</div><div className="text-center">BW</div><div className="text-right">PRICE</div>
                  </div>}
                  <div className="grid grid-cols-6 gap-1 px-3 py-2 text-sm border-b border-gray-100">
                    <div className="text-left font-medium text-blue-700">{entry.code || "-"}</div>
                    <div className="text-center">{entry.original_packs || "0"}</div>
                    <div className="text-center">{formatDecimal(entry.original_weight)}</div>
                    <div className="text-center">{entry.packs || "0"}</div>
                    <div className="text-center">{formatDecimal(entry.weight)}</div>
                    <div className="text-right font-semibold text-green-600">Rs. {formatDecimal(entry.price_per_kg || entry.PerKGPrice || entry.SalesKGPrice)}</div>
                  </div>
                </div>;
              }}
              components={{ Option: ({ innerRef, innerProps, isFocused, isSelected, data }) => (
                <div ref={innerRef} {...innerProps} className={`${isFocused ? "bg-blue-50" : ""} ${isSelected ? "bg-blue-100" : ""} cursor-pointer`}>
                  {data.index === 0 && <div className="grid grid-cols-6 gap-1 px-3 py-2 bg-gray-100 font-bold text-xs border-b border-gray-300">
                    <div className="text-left">Code</div><div className="text-center">OP</div><div className="text-center">OW</div><div className="text-center">BP</div><div className="text-center">BW</div><div className="text-right">PRICE</div>
                  </div>}
                  <div className="grid grid-cols-6 gap-1 px-3 py-2 text-sm border-b border-gray-100">
                    <div className="text-left font-medium text-blue-700">{data.data.code || "-"}</div>
                    <div className="text-center">{data.data.original_packs || "0"}</div>
                    <div className="text-center">{formatDecimal(data.data.original_weight)}</div>
                    <div className="text-center">{data.data.packs || "0"}</div>
                    <div className="text-center">{formatDecimal(data.data.weight)}</div>
                    <div className="text-right font-semibold text-green-600">Rs. {formatDecimal(data.data.price_per_kg || data.data.PerKGPrice || data.data.SalesKGPrice)}</div>
                  </div>
                </div>
              )}}
              styles={{
                option: (base) => ({ ...base, padding: 0, backgroundColor: "transparent" }),
                menu: (base) => ({ ...base, width: "650px", maxWidth: "85vw" }),
                menuList: (base) => ({ ...base, padding: 0, maxHeight: "300px" }),
                control: (base) => ({ ...base, minHeight: "44px" })
              }}
            />

            <div className="grid grid-cols-5 gap-4">
              <div className="relative">
                <input id="item_name" ref={refs.itemName} type="text" value={form.item_name} readOnly placeholder="Item Name" 
                  onKeyDown={(e) => handleKeyDown(e, 4)} className="px-4 py-2 border rounded-xl w-full" />
                {balanceInfo.balanceWeight > 0 && <div className="absolute top-full left-0 right-0 mt-1 text-xs text-gray-600 bg-yellow-50 px-2 py-1 rounded border">
                  Balance Weight: {formatDecimal(balanceInfo.balanceWeight)} kg</div>}
              </div>

              <input id="weight" ref={refs.weight} name="weight" type="number" step="0.01" value={form.weight} 
                onChange={(e) => handleInputChange(e, 5)} onKeyDown={(e) => handleKeyDown(e, 5)} placeholder="Weight (kg)" 
                className="px-4 py-2 border rounded-xl" />

              <div className="relative">
                <input id="packs" ref={refs.packs} name="packs" type="number" value={form.packs} 
                  onChange={(e) => handleInputChange(e, 6)} onKeyDown={(e) => handleKeyDown(e, 6)} placeholder="Packs" 
                  className="px-4 py-2 border rounded-xl w-full" />
                {balanceInfo.balancePacks > 0 && <div className="absolute top-full left-0 right-0 mt-1 text-xs text-gray-600 bg-yellow-50 px-2 py-1 rounded border">
                  Balance Packs: {balanceInfo.balancePacks}</div>}
              </div>

              <input id="price_per_kg" ref={refs.pricePerKg} name="price_per_kg" type="number" step="0.01" value={form.price_per_kg} 
                onChange={(e) => handleInputChange(e, 7)} onKeyDown={(e) => handleKeyDown(e, 7)} placeholder="Price/kg" 
                className="px-4 py-2 border rounded-xl" />
              <input id="total" ref={refs.total} name="total" type="number" value={form.total} readOnly placeholder="Total" 
                onKeyDown={(e) => handleKeyDown(e, 8)} className="px-4 py-2 border bg-gray-100 rounded-xl" />
            </div>
          </div>

          <div className="flex space-x-4">
            <button type="submit" className="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
              {editingSaleId ? "Update Sales Entry" : "Add Sales Entry"}
            </button>
            {editingSaleId && <button type="button" onClick={handleDeleteClick} className="py-3 px-6 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition">Delete</button>}
            <button type="button" onClick={handleClearForm} className="py-3 px-6 bg-gray-400 hover:bg-gray-500 text-white font-bold rounded-xl shadow-lg transition">Clear</button>
          </div>
        </form>

        {errors.form && <div className="mt-6 p-3 bg-red-100 text-red-700 rounded-xl">{errors.form}</div>}

        <div className="mt-10">
          <h3 className="text-xl font-bold mb-4">{selectedPrintedCustomer ? `Sales for ${selectedPrintedCustomer}` : selectedUnprintedCustomer ? `Unprinted Sales for ${selectedUnprintedCustomer}` : "All New Sales"}</h3>
          <div className="overflow-x-auto">
            <table className="min-w-full border border-gray-200 rounded-xl text-sm">
              <thead className="bg-gray-100">
                <tr>
                  <th className="px-4 py-2 border">Code</th><th className="px-4 py-2 border">Customer</th><th className="px-4 py-2 border">Item</th>
                  <th className="px-4 py-2 border">Weight (kg)</th><th className="px-4 py-2 border">Price</th><th className="px-4 py-2 border">Total</th><th className="px-4 py-2 border">Packs</th>
                </tr>
              </thead>
              <tbody>
                {displayedSales.map((s, idx) => (
                  <tr key={s.id || idx} tabIndex={0} className="text-center hover:bg-gray-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-blue-100"
                    onClick={() => handleEditClick(s)} onKeyDown={(e) => handleTableRowKeyDown(e, s)}>
                    <td className="px-4 py-2 border">{s.code}</td><td className="px-4 py-2 border">{s.customer_code}</td><td className="px-4 py-2 border">{s.item_name}</td>
                    <td className="px-4 py-2 border">{formatDecimal(s.weight)}</td><td className="px-4 py-2 border">{formatDecimal(s.price_per_kg)}</td>
                    <td className="px-4 py-2 border">{formatDecimal(s.total)}</td><td className="px-4 py-2 border">{s.packs}</td>
                  </tr>
                ))}
              </tbody>
            </table>
            <div className="flex justify-end">
              <input id="given_amount" ref={refs.givenAmount} name="given_amount" type="number" step="0.01" value={form.given_amount} 
                onChange={(e) => handleInputChange(e, 2)} onKeyDown={(e) => handleKeyDown(e, 2)} placeholder="Given Amount" 
                className="px-4 py-2 border rounded-xl mt-4" />
            </div>
          </div>
        </div>
      </div>

      <CustomerList customers={unprintedCustomers} type="unprinted" searchQuery={searchQueries.unprinted} 
        onSearchChange={(value) => setSearchQueries(prev => ({ ...prev, unprinted: value }))} 
        selectedPrintedCustomer={selectedPrintedCustomer} selectedUnprintedCustomer={selectedUnprintedCustomer}
        handleCustomerClick={handleCustomerClick} unprintedTotal={unprintedTotal} formatDecimal={formatDecimal} />
    </div>
  );
}