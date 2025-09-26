import React, { useState, useEffect, useMemo, useRef } from "react";
import Select from "react-select";

export default function SalesEntry() {
  // Initial data
  const initialData = {
    sales: window.__INITIAL_SALES__ || [],
    printed: window.__PRINTED_SALES__ || [],
    unprinted: window.__UNPRINTED_SALES__ || [],
    customers: window.__CUSTOMERS__ || [],
    entries: window.__ENTRIES__ || [],
    storeUrl: window.__STORE_URL__ || "/grn",
    csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""
  };

  // Refs and field order
  const refs = {
    customerCode: useRef(null), customerSelect: useRef(null), grnSelect: useRef(null),
    itemName: useRef(null), weight: useRef(null), packs: useRef(null),
    pricePerKg: useRef(null), total: useRef(null)
  };

  const fieldOrder = [
    { ref: refs.customerCode, name: "customer_code_input" },
    { ref: refs.customerSelect, name: "customer_code_select" },
    { ref: refs.grnSelect, name: "grn_entry_code" },
    { ref: refs.itemName, name: "item_name" },
    { ref: refs.weight, name: "weight" },
    { ref: refs.packs, name: "packs" },
    { ref: refs.pricePerKg, name: "price_per_kg" },
    { ref: refs.total, name: "total" }
  ];

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

  const [form, setForm] = useState({
    customer_code: "", customer_name: "", supplier_code: "", code: "", item_code: "",
    item_name: "", weight: "", price_per_kg: "", total: "", packs: "", grn_entry_code: "",
    original_weight: "", original_packs: "", given_amount: ""
  });

  // Derived data
  const { newSales, printedSales, unprintedSales } = useMemo(() => ({
    newSales: allSales.filter(s => s.id && !s.bill_printed),
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
    if (selectedUnprintedCustomer) {
      // Includes new sales AND unprinted sales for the selected customer
      sales = [...sales, ...unprintedSales.filter(s => s.customer_code === selectedUnprintedCustomer)];
    } else if (selectedPrintedCustomer) {
      // Includes new sales AND printed sales for the selected customer
      sales = [...sales, ...printedSales.filter(s => s.customer_code === selectedPrintedCustomer)];
    }
    return sales;
  }, [newSales, unprintedSales, printedSales, selectedUnprintedCustomer, selectedPrintedCustomer]);

  const currentBillNo = useMemo(() => 
    selectedPrintedCustomer ? printedSales.find(s => s.customer_code === selectedPrintedCustomer)?.bill_no || "N/A" : "",
    [selectedPrintedCustomer, printedSales]
  );

  const calculateTotal = (sales) => sales.reduce((acc, s) => {
    const total = parseFloat(s.total) || parseFloat(s.weight || 0) * parseFloat(s.price_per_kg || 0);
    return acc + (isNaN(total) ? 0 : total);
  }, 0);

  const mainTotal = calculateTotal(displayedSales);
  const unprintedTotal = calculateTotal(unprintedSales);

  // Helper functions
  const formatDecimal = (val) => {
    const n = parseFloat(val);
    return Number.isFinite(n) ? n.toFixed(2) : "0.00";
  };

  // Event handlers
  const handleKeyDown = (e, currentFieldIndex) => {
    if (e.key === "Enter") {
      e.preventDefault();
      if (fieldOrder[currentFieldIndex].name === "price_per_kg") {
        handleSubmit(e);
        return;
      }
      
      let nextIndex = currentFieldIndex + 1;
      const currentName = fieldOrder[currentFieldIndex].name;
      if (skipMap[currentName]) {
        const nextFieldName = skipMap[currentName];
        const targetField = fieldOrder.find(f => f.name === nextFieldName);
        if (targetField) nextIndex = fieldOrder.findIndex(f => f.name === nextFieldName);
      }

      if (nextIndex < fieldOrder.length) {
        fieldOrder[nextIndex].ref.current?.focus();
      }
    }
  };

  // 🐛 START OF FIX: Corrected handleInputChange logic
 // --- Modified handleInputChange to include field index ---
  function handleInputChange(e, fieldIndex = null) {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));

    // If the user is typing in the customer_code input
    if (name === 'customer_code') {
      const trimmedValue = value.trim();
      const matchingUnprintedCustomer = unprintedCustomers.find(
        (code) => code.toLowerCase() === trimmedValue.toLowerCase()
      );
      if (matchingUnprintedCustomer) {
        setSelectedUnprintedCustomer(matchingUnprintedCustomer);
        setSelectedPrintedCustomer(null);
      } else {
        // If no match is found, clear the selection
        if (selectedUnprintedCustomer) {
          setSelectedUnprintedCustomer(null);
        }
      }
    }

    // Auto-move to next field when customer_code reaches max length
    if (name === 'customer_code' && value.length === 10 && fieldIndex !== null) {
      const nextFieldIndex = fieldIndex + 1;
      if (nextFieldIndex < fieldOrder.length) {
        fieldOrder[nextFieldIndex].ref.current?.focus();
      }
    }
  }
  // 🔚 END OF FIX

  const handleCustomerSelect = (e) => {
    const short = e.target.value;
    const customer = initialData.customers.find(x => String(x.short_name) === String(short));
    
    // Auto-select unprinted sales when customer is selected from dropdown
    if (short) {
        const hasUnprintedSales = unprintedCustomers.includes(short);
        setSelectedUnprintedCustomer(hasUnprintedSales ? short : null);
        setSelectedPrintedCustomer(null);
    } else {
        setSelectedUnprintedCustomer(null);
        setSelectedPrintedCustomer(null);
    }

    setForm(prev => ({
      ...prev,
      customer_code: short || prev.customer_code,
      customer_name: customer?.name || ""
    }));
  };

  const handleEditClick = (sale) => {
    setForm({ ...sale });
    setEditingSaleId(sale.id);
  };

  const handleClearForm = () => {
    setForm({
      customer_code: "", customer_name: "", supplier_code: "", code: "", item_code: "",
      item_name: "", weight: "", price_per_kg: "", total: "", packs: "", grn_entry_code: "",
      original_weight: "", original_packs: "", given_amount: ""
    });
    setEditingSaleId(null);
    setGrnSearchInput("");
  };

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
      if (!res.ok) throw new Error(data.error || "Server error");
      return data;
    } catch (error) {
      throw error;
    }
  };

  const handleDeleteClick = async () => {
    if (!editingSaleId || !window.confirm("Are you sure you want to delete this sales record?")) return;

    try {
      await apiCall(`/sales/${editingSaleId}`, "DELETE");
      setAllSales(prev => prev.filter(s => s.id !== editingSaleId));
      handleClearForm();
      alert("Record deleted successfully.");
    } catch (error) {
      setErrors({ form: error.message });
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setErrors({});

    const payload = {
      supplier_code: form.supplier_code,
      customer_code: (form.customer_code || "").toString().toUpperCase(),
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
      given_amount: form.given_amount ? parseFloat(form.given_amount) : null,
    };

    try {
      const isEditing = editingSaleId !== null;
      const url = isEditing ? `/sales/${editingSaleId}` : initialData.storeUrl;
      const method = isEditing ? "PUT" : "POST";

      const data = await apiCall(url, method, payload);
      const newSale = isEditing ? data.sale : data.data || {};

      setAllSales(prev => isEditing ? prev.map(s => s.id === newSale.id ? newSale : s) : [...prev, newSale]);
      handleClearForm();
      refs.customerCode.current?.focus();
    } catch (error) {
      setErrors({ form: error.message });
    }
  };

  // Print functions
  const buildFullReceiptHTML = (salesData, billNo, customerName) => {
    const date = new Date().toLocaleDateString();
    const time = new Date().toLocaleTimeString();
    let totalAmountSum = 0;
    let totalPacksSum = 0;

    const itemsHtml = salesData.map(s => {
      totalAmountSum += parseFloat(s.total) || 0;
      totalPacksSum += parseInt(s.packs) || 0;
      return `<tr style="font-size:1.2em;">
        <td style="text-align:left;">${s.item_name || ""} <br>${s.packs || 0}</td>
        <td style="text-align:right; padding-right:18px;">${(parseFloat(s.weight) || 0).toFixed(2)}</td>
        <td style="text-align:right;">${(parseFloat(s.price_per_kg) || 0).toFixed(2)}</td>
        <td style="text-align:right;">${(parseFloat(s.total) || 0).toFixed(2)}</td>
      </tr>`;
    }).join("");

    const packCostTotal = window.globalTotalPackCostValue || 0;
    const totalPrice = totalAmountSum;
    const givenAmount = salesData.reduce((sum, s) => sum + (parseFloat(s.given_amount) || 0), 0);
    const remaining = givenAmount - (totalPrice + packCostTotal);

    return `<div style="width:100%; max-width:300px; margin:0 auto; padding:5px; font-family:sans-serif;">
      <div style="text-align:center;">
        <h3>B32 TAG ට්‍රේඩර්ස්</h3>
        <p>අල, ෆී ළූනු, කුළුබඩු තොග ගෙන්වන්නෝ බෙදාහරින්නෝ</p>
        <p>වි.ආ.ම. වේයන්ගොඩ</p>
      </div>
      <hr>
      <table style="width:100%; font-size:9px; border-collapse:collapse;">
        <tr><td>දිනය: ${date}</td><td style="text-align:right;">${time}</td></tr>
        <tr><td>බිල් අංකය: <strong>${billNo}</strong></td><td style="text-align:right;">${customerName}</td></tr>
      </table>
      <hr>
      <table style="width:100%; border-collapse:collapse;">
        <thead><tr><th>වර්ගය</th><th>කිලෝ</th><th>මිල</th><th>අගය</th></tr></thead>
        <tbody>${itemsHtml}</tbody>
      </table>
      <hr>
      <table style="width:100%; font-size:11px;">
        <tr><td>මුළු කුලිය:</td><td style="text-align:right;">${packCostTotal.toFixed(2)}</td></tr>
        <tr><td>මුළු අගය:</td><td style="text-align:right;">${(totalPrice + packCostTotal).toFixed(2)}</td></tr>
        ${givenAmount > 0 ? `<tr>
          <td style="width:50%; text-align:left;">දුන් මුදල: <strong>${givenAmount.toFixed(2)}</strong></td>
          <td style="width:50%; text-align:right;">ඉතිරිය: <strong>${Math.abs(remaining).toFixed(2)}</strong></td>
        </tr>` : ''}
      </table>
      <hr>
      <div style="text-align:center; font-size:10px;">
        <p>භාණ්ඩ පරීක්ෂාකර බලා රැගෙන යන්න</p>
        <p>නැවත භාර ගනු නොලැබේ</p>
      </div>
    </div>`;
  };

  const printReceipt = (html, customerName) => {
    return new Promise((resolve) => {
      const printWindow = window.open("", "_blank");
      printWindow.document.write(`<!DOCTYPE html><html><head><title>${customerName}</title></head><body>${html}</body></html>`);
      printWindow.document.close();
      printWindow.focus();
      printWindow.print();
      requestAnimationFrame(() => setTimeout(() => { printWindow.close(); resolve(); }, 500));
    });
  };

  const handlePrintAndClear = async () => {
    const salesData = displayedSales.filter(s => s.id);
    if (!salesData.length) return alert("No sales records to print!");

    try {
      const data = await apiCall("/sales/mark-printed", "POST", {
        sales_ids: salesData.map(s => s.id)
      });

      if (data.status !== "success") throw new Error(data.message || "Unknown error");

      const customerName = salesData[0].customer_code || "N/A";
      const billNo = data.bill_no || "";

      await printReceipt(buildFullReceiptHTML(salesData, billNo, customerName), customerName);
      await printReceipt(`<div style="text-align:center;font-size:2em;font-weight:bold;color:red;margin-bottom:10px;">COPY</div>` +
        buildFullReceiptHTML(salesData, billNo, customerName), customerName + " - Copy");

      setAllSales(prev => prev.map(s =>
        salesData.map(d => d.id).includes(s.id) ? { ...s, bill_printed: 'Y', bill_no: billNo } : s
      ));

      setSelectedUnprintedCustomer(null);
      setSelectedPrintedCustomer(null);
    } catch (error) {
      alert("Printing failed: " + error.message);
    }
  };

  // Effects
  useEffect(() => {
    const handleShortcut = (e) => {
      if (e.key === "F1") {
        e.preventDefault();
        handlePrintAndClear();
      } else if (e.key === "F5") {
        e.preventDefault();
        if (newSales.length === 0) return alert("No new sales to process.");
        if (window.confirm("Are you sure you want to mark ALL sales as processed?")) {
          apiCall("/sales/mark-all-processed", "POST", { sales_ids: newSales.map(s => s.id) })
            .then(data => {
              if (data.success) {
                alert(data.message || "All sales marked as processed successfully!");
                setAllSales(prev => prev.map(s =>
                  newSales.map(ns => ns.id).includes(s.id) ? { ...s, bill_printed: "N" } : s
                ));
              } else {
                alert(data.message || "Failed to mark sales as processed.");
              }
            })
            .catch(err => alert("Failed to mark sales as processed."));
        }
      }
    };

    window.addEventListener("keydown", handleShortcut);
    return () => window.removeEventListener("keydown", handleShortcut);
  }, [displayedSales, newSales]);

  useEffect(() => {
    const w = parseFloat(form.weight) || 0;
    const p = parseFloat(form.price_per_kg) || 0;
    setForm(prev => ({ ...prev, total: w * p ? Number((w * p).toFixed(2)) : "" }));
  }, [form.weight, form.price_per_kg]);

  useEffect(() => {
    refs.customerCode.current?.focus();
  }, []);

  const handleCustomerClick = (type, customerCode) => {
    if (type === 'printed') {
      setSelectedPrintedCustomer(customerCode === selectedPrintedCustomer ? null : customerCode);
      setSelectedUnprintedCustomer(null);
    } else {
      setSelectedUnprintedCustomer(customerCode === selectedUnprintedCustomer ? null : customerCode);
      setSelectedPrintedCustomer(null);
    }
    // Update the form code to the selected customer's code
    const customer = initialData.customers.find(x => String(x.short_name) === String(customerCode));
    setForm(prev => ({
        ...prev,
        customer_code: customerCode,
        customer_name: customer?.name || ""
    }));
  };

  // Components
  const CustomerList = ({ customers, sales, type, searchQuery, onSearchChange }) => (
    <div className="w-1/5 bg-white shadow-xl rounded-xl p-4 overflow-y-auto max-h-screen">
      <h2 className="text-xl font-bold mb-4">{type === 'printed' ? 'Printed Customers' : 'Unprinted Sales'}</h2>

      {type === 'unprinted' && (
        <div className="bg-gray-50 p-3 rounded-xl shadow-sm mb-4">
          <h3 className="text-lg font-semibold text-gray-800">
            Total Unprinted: <span className="text-red-600 font-bold">Rs. {formatDecimal(unprintedTotal)}</span>
          </h3>
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

      {customers.length === 0 ? (
        <p className="text-gray-500">No {type === 'printed' ? 'printed sales' : 'unprinted sales'} found.</p>
      ) : (
        <ul>
          {customers.map(customerCode => (
            <li key={customerCode}>
              <button
                onClick={() => handleCustomerClick(type, customerCode)}
                className={`w-full text-left p-3 mb-2 rounded-xl border ${(type === 'printed' ? selectedPrintedCustomer : selectedUnprintedCustomer) === customerCode
                    ? "bg-blue-500 text-white border-blue-600"
                    : "bg-gray-50 hover:bg-gray-100 border-gray-200"
                  }`}
              >
                <div className="font-medium">{customerCode}</div>
                <div className="text-sm text-gray-600">
                  Sales: {sales.filter(s => s.customer_code === customerCode).length}
                </div>
              </button>
            </li>
          ))}
        </ul>
      )}
    </div>
  );

  // Main render
  return (
    <div className="min-h-screen flex flex-row bg-gray-100 p-6">
      <CustomerList
        customers={printedCustomers}
        sales={printedSales}
        type="printed"
        searchQuery={searchQueries.printed}
        onSearchChange={(value) => setSearchQueries(prev => ({ ...prev, printed: value }))}
      />

      <div className="w-3/5 bg-white shadow-2xl rounded-3xl p-10 mx-6">
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="flex justify-between items-center bg-gray-50 p-4 rounded-xl shadow-sm">
            <span className="text-gray-600 font-medium">Bill No: {currentBillNo}</span>
            <h2 className="text-2xl font-bold text-red-600">Total Sales: Rs. {formatDecimal(mainTotal)}</h2>
          </div>

          <div className="grid grid-cols-1 gap-4">
            <div className="grid grid-cols-2 gap-4">
              <input
                ref={refs.customerCode}
                name="customer_code"
                value={form.customer_code}
                onChange={(e) => handleInputChange(e, 0)}
                onKeyDown={(e) => handleKeyDown(e, 0)}
                type="text"
                maxLength={10}
                placeholder="Customer Code"
                className="px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300"
              />

              <select
                ref={refs.customerSelect}
                value={form.customer_code}
                onChange={handleCustomerSelect}
                onKeyDown={(e) => handleKeyDown(e, 1)}
                className="px-4 py-2 border rounded-xl"
              >
                <option value="">-- Select Customer --</option>
                {initialData.customers.map(c => (
                  <option key={c.short_name} value={c.short_name}>
                    {c.name} ({c.short_name})
                  </option>
                ))}
              </select>
            </div>

            <Select
              ref={refs.grnSelect}
              value={form.grn_entry_code ? {
                value: form.grn_entry_code,
                label: `${form.grn_entry_code} - ${form.item_name}`,
                data: initialData.entries.find(en => en.code === form.grn_entry_code)
              } : null}
              onChange={(selected) => {
                if (selected && selected.data) {
                  const entry = selected.data;
                  setForm(prev => ({
                    ...prev,
                    grn_entry_code: selected.value,
                    item_name: entry.item_name || "",
                    supplier_code: entry.supplier_code || "",
                    item_code: entry.item_code || "",
                    price_per_kg: entry.price_per_kg || entry.PerKGPrice || entry.SalesKGPrice || "",
                    weight: "", packs: "", total: ""
                  }));
                  setGrnSearchInput("");
                  requestAnimationFrame(() => {
                    setTimeout(() => refs.weight.current?.focus(), 10);
                  });
                }
              }}
              onInputChange={setGrnSearchInput}
              onKeyDown={(e) => {
                if (e.key === "Enter" && form.grn_entry_code && !e.isPropagationStopped()) {
                  e.preventDefault();
                  setTimeout(() => refs.weight.current?.focus(), 0);
                }
              }}
              onMenuClose={() => setTimeout(() => form.grn_entry_code ? refs.weight.current?.focus() : refs.grnSelect.current?.focus(), 0)}
              getOptionLabel={(option) => `${option.data?.code} - ${option.data?.item_name || 'Unknown Item'}`}
              getOptionValue={(option) => option.value}
              options={initialData.entries.map((en, index) => ({
                value: en.code, label: en.code, data: en, index
              }))}
              placeholder="Select GRN Entry"
              isSearchable={true}
              noOptionsMessage={() => "No GRN entries found"}
              formatOptionLabel={(option, { context }) => {
                if (context === "value" || !option.data) return <span>{option.label} - {option.data?.item_name || "Unknown Item"}</span>;
                
                const entry = option.data;
                return (
                  <div className="w-full">
                    {option.index === 0 && (
                      <div className="grid grid-cols-6 gap-1 px-3 py-2 bg-gray-100 font-bold text-xs border-b border-gray-300">
                        <div className="text-left">Code</div><div className="text-center">OP</div><div className="text-center">OW</div>
                        <div className="text-center">BP</div><div className="text-center">BW</div><div className="text-right">PRICE</div>
                      </div>
                    )}
                    <div className="grid grid-cols-6 gap-1 px-3 py-2 text-sm border-b border-gray-100">
                      <div className="text-left font-medium text-blue-700">{entry.code || "-"}</div>
                      <div className="text-center">{entry.original_packs || "0"}</div>
                      <div className="text-center">{formatDecimal(entry.original_weight)}</div>
                      <div className="text-center">{entry.packs || "0"}</div>
                      <div className="text-center">{formatDecimal(entry.weight)}</div>
                      <div className="text-right font-semibold text-green-600">
                        Rs. {formatDecimal(entry.price_per_kg || entry.PerKGPrice || entry.SalesKGPrice)}
                      </div>
                    </div>
                  </div>
                );
              }}
              components={{
                Option: ({ innerRef, innerProps, isFocused, isSelected, data }) => (
                  <div ref={innerRef} {...innerProps} className={`${isFocused ? 'bg-blue-50' : ''} ${isSelected ? 'bg-blue-100' : ''} cursor-pointer`}>
                    <div className="w-full">
                      {data.index === 0 && (
                        <div className="grid grid-cols-6 gap-1 px-3 py-2 bg-gray-100 font-bold text-xs border-b border-gray-300">
                          <div className="text-left">Code</div><div className="text-center">OP</div><div className="text-center">OW</div>
                          <div className="text-center">BP</div><div className="text-center">BW</div><div className="text-right">PRICE</div>
                        </div>
                      )}
                      <div className="grid grid-cols-6 gap-1 px-3 py-2 text-sm border-b border-gray-100">
                        <div className="text-left font-medium text-blue-700">{data.data.code || "-"}</div>
                        <div className="text-center">{data.data.original_packs || "0"}</div>
                        <div className="text-center">{formatDecimal(data.data.original_weight)}</div>
                        <div className="text-center">{data.data.packs || "0"}</div>
                        <div className="text-center">{formatDecimal(data.data.weight)}</div>
                        <div className="text-right font-semibold text-green-600">
                          Rs. {formatDecimal(data.data.price_per_kg || data.data.PerKGPrice || data.data.SalesKGPrice)}
                        </div>
                      </div>
                    </div>
                  </div>
                )
              }}
              styles={{
                option: (base) => ({ ...base, padding: 0, backgroundColor: "transparent" }),
                menu: (base) => ({ ...base, width: "650px", maxWidth: "85vw" }),
                menuList: (base) => ({ ...base, padding: 0, maxHeight: "300px" }),
                control: (base) => ({ ...base, minHeight: "44px" })
              }}
            />

            <div className="grid grid-cols-5 gap-4">
              <input ref={refs.itemName} type="text" value={form.item_name} readOnly placeholder="Item Name" onKeyDown={(e) => handleKeyDown(e, 3)} className="px-4 py-2 border rounded-xl" />
              <input ref={refs.weight} name="weight" type="number" step="0.01" value={form.weight} onChange={(e) => handleInputChange(e, 4)} onKeyDown={(e) => handleKeyDown(e, 4)} placeholder="Weight (kg)" className="px-4 py-2 border rounded-xl" />
              <input ref={refs.packs} name="packs" type="number" value={form.packs} onChange={(e) => handleInputChange(e, 5)} onKeyDown={(e) => handleKeyDown(e, 5)} placeholder="Packs" className="px-4 py-2 border rounded-xl" />
              <input ref={refs.pricePerKg} name="price_per_kg" type="number" step="0.01" value={form.price_per_kg} onChange={(e) => handleInputChange(e, 6)} onKeyDown={(e) => handleKeyDown(e, 6)} placeholder="Price/kg" className="px-4 py-2 border rounded-xl" />
              <input ref={refs.total} name="total" type="number" value={form.total} readOnly placeholder="Total" onKeyDown={(e) => handleKeyDown(e, 7)} className="px-4 py-2 border bg-gray-100 rounded-xl" />
            </div>
          </div>

          <div className="flex space-x-4">
            <button type="submit" className="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
              {editingSaleId ? "Update Sales Entry" : "Add Sales Entry"}
            </button>
            {editingSaleId && (
              <button type="button" onClick={handleDeleteClick} className="py-3 px-6 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition">
                Delete
              </button>
            )}
            <button type="button" onClick={handleClearForm} className="py-3 px-6 bg-gray-400 hover:bg-gray-500 text-white font-bold rounded-xl shadow-lg transition">
              Clear
            </button>
          </div>
        </form>

        {errors.form && <div className="mt-6 p-3 bg-red-100 text-red-700 rounded-xl">{errors.form}</div>}

        <div className="mt-10">
          <h3 className="text-xl font-bold mb-4">
            {selectedPrintedCustomer ? `Sales for ${selectedPrintedCustomer}` :
              selectedUnprintedCustomer ? `Unprinted Sales for ${selectedUnprintedCustomer}` : "All New Sales"}
          </h3>
          <div className="overflow-x-auto">
            <table className="min-w-full border border-gray-200 rounded-xl text-sm">
              <thead className="bg-gray-100">
                <tr>
                  <th className="px-4 py-2 border">Code</th><th className="px-4 py-2 border">Customer</th><th className="px-4 py-2 border">Item</th>
                  <th className="px-4 py-2 border">Weight (kg)</th><th className="px-4 py-2 border">Price</th><th className="px-4 py-2 border">Total</th>
                  <th className="px-4 py-2 border">Packs</th>
                </tr>
              </thead>
              <tbody>
                {displayedSales.map((s, idx) => (
                  <tr key={s.id || idx} className="text-center hover:bg-gray-50 cursor-pointer" onClick={() => handleEditClick(s)}>
                    <td className="px-4 py-2 border">{s.code}</td><td className="px-4 py-2 border">{s.customer_code}</td>
                    <td className="px-4 py-2 border">{s.item_name}</td><td className="px-4 py-2 border">{formatDecimal(s.weight)}</td>
                    <td className="px-4 py-2 border">{formatDecimal(s.price_per_kg)}</td><td className="px-4 py-2 border">{formatDecimal(s.total)}</td>
                    <td className="px-4 py-2 border">{s.packs}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <CustomerList
        customers={unprintedCustomers}
        sales={unprintedSales}
        type="unprinted"
        searchQuery={searchQueries.unprinted}
        onSearchChange={(value) => setSearchQueries(prev => ({ ...prev, unprinted: value }))}
      />
    </div>
  );
}