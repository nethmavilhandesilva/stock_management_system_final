import React, { useState, useEffect, useMemo, useRef } from "react";
import Select from "react-select";

export default function SalesEntry() {
  // Initial data
  const initialData = {
    sales: (window.__INITIAL_SALES__ || []).filter(s => s.id),
    printed: (window.__PRINTED_SALES__ || []).filter(s => s.id),
    unprinted: (window.__UNPRINTED_SALES__ || []).filter(s => s.id),
    customers: window.__CUSTOMERS__ || [],
    entries: window.__ENTRIES__ || [],
    storeUrl: window.__STORE_URL__ || "/grn",
    csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || ""
  };

  // Refs
  const refs = {
    customerCode: useRef(null), customerSelect: useRef(null), givenAmount: useRef(null),
    grnSelect: useRef(null), itemName: useRef(null), weight: useRef(null),
    packs: useRef(null), pricePerKg: useRef(null), total: useRef(null)
  };

  // New ref for the sales table body
  const salesTableBodyRef = useRef(null);

  const fieldOrder = ["customer_code_input", "customer_code_select", "given_amount", "grn_entry_code", "item_name", "weight", "packs", "price_per_kg", "total"];
  const skipMap = { customer_code_input: "grn_entry_code", grn_entry_code: "weight" };

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

  // New state for balance information
  const [balanceInfo, setBalanceInfo] = useState({
    balancePacks: 0,
    balanceWeight: 0
  });

  // New state to track if we should allow auto-focus
  const [allowAutoFocus, setAllowAutoFocus] = useState(true);

  // Debug useEffect
  useEffect(() => {
    console.log('Form updated:', form);
    console.log('grn_entry_code:', form.grn_entry_code);
    console.log('Matching entry:', initialData.entries.find((en) => en.code === form.grn_entry_code));
  }, [form]);

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

  // Update balance info when GRN entry changes
  useEffect(() => {
    if (form.grn_entry_code) {
      const matchingEntry = initialData.entries.find((en) => en.code === form.grn_entry_code);
      if (matchingEntry) {
        setBalanceInfo({
          balancePacks: matchingEntry.packs || 0,
          balanceWeight: matchingEntry.weight || 0
        });
      }
    } else {
      setBalanceInfo({
        balancePacks: 0,
        balanceWeight: 0
      });
    }
  }, [form.grn_entry_code, initialData.entries]);

  // Simplified focus function
  const focusField = (fieldName) => {
    if (!allowAutoFocus) return;
    
    const fieldIndex = fieldOrder.indexOf(fieldName);
    if (fieldIndex === -1) return;

    const fieldRef = Object.values(refs)[fieldIndex];
    if (fieldRef && fieldRef.current) {
      setTimeout(() => {
        if (fieldRef.current.focus) {
          fieldRef.current.focus();
        } else if (fieldRef.current.select) {
          fieldRef.current.select.focus();
        }
      }, 10);
    }
  };

  // Event handlers
  const handleKeyDown = (e, currentFieldIndex) => {
    if (e.key === "Enter") {
      e.preventDefault();

      // If we're in the given_amount field and it has a value, submit only the given amount
      if (fieldOrder[currentFieldIndex] === "given_amount" && form.given_amount) {
        handleSubmitGivenAmount(e);
        return;
      }

      if (fieldOrder[currentFieldIndex] === "price_per_kg") return handleSubmit(e);

      let nextIndex = currentFieldIndex + 1;
      if (skipMap[fieldOrder[currentFieldIndex]]) {
        const targetIndex = fieldOrder.findIndex(f => f === skipMap[fieldOrder[currentFieldIndex]]);
        if (targetIndex !== -1) nextIndex = targetIndex;
      }
      
      if (nextIndex < fieldOrder.length) {
        focusField(fieldOrder[nextIndex]);
      }
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

        if (nextIndex < fieldOrder.length) {
          focusField(fieldOrder[nextIndex]);
        }
      }
    }
  };

  // Handle mouse down on fields - temporarily disable auto-focus
  const handleFieldMouseDown = (fieldName) => {
    setAllowAutoFocus(false);
    setTimeout(() => setAllowAutoFocus(true), 100);
  };

  const handleCustomerSelect = (e) => {
    const short = e.target.value;
    const customer = initialData.customers.find(x => String(x.short_name) === String(short));
    const hasUnprintedSales = unprintedCustomers.includes(short);

    setSelectedUnprintedCustomer(hasUnprintedSales ? short : null);
    setSelectedPrintedCustomer(null);
    setForm(prev => ({ ...prev, customer_code: short || prev.customer_code, customer_name: customer?.name || "" }));
    
    // Only auto-focus if allowed
    if (allowAutoFocus) {
      setTimeout(() => focusField("grn_entry_code"), 10);
    }
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
      given_amount: sale.given_amount || ""
    });
    setEditingSaleId(sale.id);
    
    // Focus on the first field when editing
    setTimeout(() => focusField("customer_code_input"), 10);
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
    
    // Focus on customer code after clear
    setTimeout(() => focusField("customer_code_input"), 10);
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
      if (!res.ok) throw new Error(data.error || "Server error: " + res.statusText);
      return data;
    } catch (error) { throw error; }
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

  // New function to handle given amount submission
  const handleSubmitGivenAmount = async (e) => {
    e.preventDefault();
    setErrors({});

    if (!form.customer_code) {
      setErrors({ form: "Please enter a customer code first" });
      focusField("customer_code_input");
      return;
    }

    if (!form.given_amount) {
      setErrors({ form: "Please enter a given amount" });
      focusField("given_amount");
      return;
    }

    // Find the first sales record for this customer
    const customerSales = allSales.filter(s => s.customer_code === form.customer_code);
    const firstSale = customerSales[0];

    if (!firstSale) {
      setErrors({ form: "No sales records found for this customer. Please add a sales record first." });
      return;
    }

    const payload = {
      given_amount: parseFloat(form.given_amount) || 0
    };

    try {
      // Use the specific endpoint for given_amount
      const data = await apiCall(`/sales/${firstSale.id}/given-amount`, "PUT", payload);
      const updatedSale = data.sale;

      setAllSales(prev => prev.map(s => s.id === updatedSale.id ? updatedSale : s));

      // Clear only the given_amount field and move to next field
      setForm(prev => ({ ...prev, given_amount: "" }));
      focusField("grn_entry_code");

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

    // Check if this is the first record for this customer
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
      // Only set given_amount for the first record of a customer or when editing the first record
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
      if (!isEditing && billPrintedStatus && !newSale.bill_printed)
        newSale = { ...newSale, bill_printed: billPrintedStatus };

      setAllSales(prev => isEditing
        ? prev.map(s => s.id === newSale.id ? newSale : s)
        : [...prev, newSale]
      );

      // Clear form but preserve customer_code and customer_name
      setForm(prevForm => ({
        customer_code: prevForm.customer_code,
        customer_name: prevForm.customer_name,
        supplier_code: "",
        code: "",
        item_code: "",
        item_name: "",
        weight: "",
        price_per_kg: "",
        total: "",
        packs: "",
        grn_entry_code: "",
        original_weight: "",
        original_packs: "",
        given_amount: ""
      }));

      setEditingSaleId(null);
      setGrnSearchInput("");
      setBalanceInfo({ balancePacks: 0, balanceWeight: 0 });

      focusField("grn_entry_code");
    } catch (error) {
      setErrors({ form: error.message });
    }
  };

  // ... (rest of your existing functions like buildFullReceiptHTML, printReceipt, handlePrintAndClear remain the same)

  // Update the useEffect for F1 and F5 shortcuts
  useEffect(() => {
    const handleShortcut = (e) => {
      if (e.key === "F1") {
        e.preventDefault();
        handlePrintAndClear().finally(() => {
          setTimeout(() => focusField("customer_code_input"), 100);
        });
      } else if (e.key === "F5") {
        e.preventDefault();
        // ... (your existing F5 logic)
        setTimeout(() => focusField("customer_code_input"), 100);
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
    focusField("customer_code_input"); 
  }, []);

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
    setForm(prev => ({
      ...prev,
      customer_code: isCurrentlySelected ? "" : customerCode,
      customer_name: isCurrentlySelected ? "" : customer?.name || ""
    }));
  };

  // Components
  const CustomerList = ({ customers, sales, type, searchQuery, onSearchChange }) => (
    <div className="w-1/5 bg-white shadow-xl rounded-xl p-4 overflow-y-auto max-h-screen">
      <h2 className="text-xl font-bold mb-4">{type === 'printed' ? 'Printed Customers' : 'Unprinted Sales'}</h2>
      {type === 'unprinted' && (
        <div className="bg-gray-50 p-3 rounded-xl shadow-sm mb-4">
          <h3 className="text-lg font-semibold text-gray-800">Total Unprinted: <span className="text-red-600 font-bold">Rs. {formatDecimal(unprintedTotal)}</span></h3>
        </div>
      )}
      <div className="mb-4">
        <input type="text" placeholder={`Search by ${type === 'printed' ? 'Bill No or Code...' : 'Customer Code...'}`} value={searchQuery} onChange={e => onSearchChange(e.target.value)} className="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300" />
      </div>
      {customers.length === 0 ? <p className="text-gray-500">No {type === 'printed' ? 'printed sales' : 'unprinted sales'} found.</p> : (
        <ul>{customers.map(customerCode => (
          <li key={customerCode}>
            <button onClick={() => handleCustomerClick(type, customerCode)} className={`w-full text-left p-3 mb-2 rounded-xl border ${(type === 'printed' ? selectedPrintedCustomer : selectedUnprintedCustomer) === customerCode ? "bg-blue-500 text-white border-blue-600" : "bg-gray-50 hover:bg-gray-100 border-gray-200"}`}>
              <div className="font-medium">{customerCode}</div>
              <div className="text-sm text-gray-600">Sales: {sales.filter(s => s.customer_code === customerCode).length}</div>
            </button>
          </li>
        ))}</ul>
      )}
    </div>
  );

  // Main render - updated with mouse event handlers
  return (
    <div className="min-h-screen flex flex-row bg-gray-100 p-6">
      <CustomerList customers={printedCustomers} sales={printedSales} type="printed" searchQuery={searchQueries.printed} onSearchChange={(value) => setSearchQueries(prev => ({ ...prev, printed: value }))} />

      <div className="w-3/5 bg-white shadow-2xl rounded-3xl p-10 mx-6">
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="flex justify-between items-center bg-gray-50 p-4 rounded-xl shadow-sm">
            <span className="text-gray-600 font-medium">Bill No: {currentBillNo}</span>
            <h2 className="text-2xl font-bold text-red-600">Total Sales: Rs. {formatDecimal(mainTotal)}</h2>
          </div>

          <div className="grid grid-cols-1 gap-4">
            <div className="grid grid-cols-3 gap-4">
              <input 
                id="customer_code_input" 
                ref={refs.customerCode} 
                name="customer_code" 
                value={form.customer_code} 
                onChange={(e) => handleInputChange(e, 0)} 
                onKeyDown={(e) => handleKeyDown(e, 0)}
                onMouseDown={() => handleFieldMouseDown("customer_code_input")}
                type="text" 
                maxLength={10} 
                placeholder="Customer Code" 
                className="px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300" 
              />
              <select 
                id="customer_code_select" 
                ref={refs.customerSelect} 
                value={form.customer_code} 
                onChange={handleCustomerSelect} 
                onKeyDown={(e) => handleKeyDown(e, 1)}
                onMouseDown={() => handleFieldMouseDown("customer_code_select")}
                className="px-4 py-2 border rounded-xl"
              >
                <option value="">-- Select Customer --</option>
                {initialData.customers.map(c => <option key={c.short_name} value={c.short_name}>{c.name} ({c.short_name})</option>)}
              </select>
              <input 
                id="given_amount" 
                ref={refs.givenAmount} 
                name="given_amount" 
                type="number" 
                step="0.01" 
                value={form.given_amount} 
                onChange={(e) => handleInputChange(e, 2)} 
                onKeyDown={(e) => handleKeyDown(e, 2)}
                onMouseDown={() => handleFieldMouseDown("given_amount")}
                placeholder="Given Amount" 
                className="px-4 py-2 border rounded-xl" 
              />
            </div>

            <Select
              id="grn_entry_code"
              ref={refs.grnSelect}
              value={(() => {
                if (!form.grn_entry_code) return null;

                const matchingEntry = initialData.entries.find((en) => en.code === form.grn_entry_code);
                if (!matchingEntry) return null;

                return {
                  value: form.grn_entry_code,
                  label: `${form.grn_entry_code} - ${matchingEntry.item_name || form.item_name || ''}`,
                  data: matchingEntry
                };
              })()}
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
                    // Only clear weight and packs if NOT editing
                    weight: editingSaleId ? prev.weight : "",
                    packs: editingSaleId ? prev.packs : "",
                    total: editingSaleId ? prev.total : ""
                  }));
                  setGrnSearchInput("");
                  setTimeout(() => focusField("weight"), 10);
                }
              }}
              onInputChange={setGrnSearchInput}
              onKeyDown={(e) => {
                if (e.key === "Enter" && form.grn_entry_code && !e.isPropagationStopped()) {
                  e.preventDefault();
                  setTimeout(() => focusField("weight"), 0);
                }
              }}
              onMenuClose={() => setTimeout(() => form.grn_entry_code ? focusField("weight") : focusField("grn_entry_code"), 0)}
              getOptionLabel={(option) => `${option.data?.code} - ${option.data?.item_name || "Unknown Item"}`}
              getOptionValue={(option) => option.value}
              options={initialData.entries.map((en, index) => ({
                value: en.code,
                label: en.code,
                data: en,
                index
              }))}
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
                  {option.index === 0 && <div className="grid grid-cols-6 gap-1 px-3 py-2 bg-gray-100 font-bold text-xs border-b border-gray-300"><div className="text-left">Code</div><div className="text-center">OP</div><div className="text-center">OW</div><div className="text-center">BP</div><div className="text-center">BW</div><div className="text-right">PRICE</div></div>}
                  <div className="grid grid-cols-6 gap-1 px-3 py-2 text-sm border-b border-gray-100">
                    <div className="text-left font-medium text-blue-700">{entry.code || "-"}</div><div className="text-center">{entry.original_packs || "0"}</div><div className="text-center">{formatDecimal(entry.original_weight)}</div><div className="text-center">{entry.packs || "0"}</div><div className="text-center">{formatDecimal(entry.weight)}</div><div className="text-right font-semibold text-green-600">Rs. {formatDecimal(entry.price_per_kg || entry.PerKGPrice || entry.SalesKGPrice)}</div>
                  </div>
                </div>;
              }}
              components={{
                Option: ({ innerRef, innerProps, isFocused, isSelected, data }) => (
                  <div ref={innerRef} {...innerProps} className={`${isFocused ? "bg-blue-50" : ""} ${isSelected ? "bg-blue-100" : ""} cursor-pointer`}>
                    <div className="w-full">
                      {data.index === 0 && <div className="grid grid-cols-6 gap-1 px-3 py-2 bg-gray-100 font-bold text-xs border-b border-gray-300"><div className="text-left">Code</div><div className="text-center">OP</div><div className="text-center">OW</div><div className="text-center">BP</div><div className="text-center">BW</div><div className="text-right">PRICE</div></div>}
                      <div className="grid grid-cols-6 gap-1 px-3 py-2 text-sm border-b border-gray-100">
                        <div className="text-left font-medium text-blue-700">{data.data.code || "-"}</div><div className="text-center">{data.data.original_packs || "0"}</div><div className="text-center">{formatDecimal(data.data.original_weight)}</div><div className="text-center">{data.data.packs || "0"}</div><div className="text-center">{formatDecimal(data.data.weight)}</div><div className="text-right font-semibold text-green-600">Rs. {formatDecimal(data.data.price_per_kg || data.data.PerKGPrice || data.data.SalesKGPrice)}</div>
                      </div>
                    </div>
                  </div>
                )
              }}
              styles={{ option: (base) => ({ ...base, padding: 0, backgroundColor: "transparent" }), menu: (base) => ({ ...base, width: "650px", maxWidth: "85vw" }), menuList: (base) => ({ ...base, padding: 0, maxHeight: "300px" }), control: (base) => ({ ...base, minHeight: "44px" }) }}
            />

            <div className="grid grid-cols-5 gap-4">
              <div className="relative">
                <input 
                  id="item_name" 
                  ref={refs.itemName} 
                  type="text" 
                  value={form.item_name} 
                  readOnly 
                  placeholder="Item Name" 
                  onKeyDown={(e) => handleKeyDown(e, 4)}
                  onMouseDown={() => handleFieldMouseDown("item_name")}
                  className="px-4 py-2 border rounded-xl w-full" 
                />
                {balanceInfo.balanceWeight > 0 && (
                  <div className="absolute top-full left-0 right-0 mt-1 text-xs text-gray-600 bg-yellow-50 px-2 py-1 rounded border">
                    Balance Weight: {formatDecimal(balanceInfo.balanceWeight)} kg
                  </div>
                )}
              </div>

              <input 
                id="weight" 
                ref={refs.weight} 
                name="weight" 
                type="number" 
                step="0.01" 
                value={form.weight} 
                onChange={(e) => handleInputChange(e, 5)} 
                onKeyDown={(e) => handleKeyDown(e, 5)}
                onMouseDown={() => handleFieldMouseDown("weight")}
                placeholder="Weight (kg)" 
                className="px-4 py-2 border rounded-xl" 
              />

              <div className="relative">
                <input 
                  id="packs" 
                  ref={refs.packs} 
                  name="packs" 
                  type="number" 
                  value={form.packs} 
                  onChange={(e) => handleInputChange(e, 6)} 
                  onKeyDown={(e) => handleKeyDown(e, 6)}
                  onMouseDown={() => handleFieldMouseDown("packs")}
                  placeholder="Packs" 
                  className="px-4 py-2 border rounded-xl w-full" 
                />
                {balanceInfo.balancePacks > 0 && (
                  <div className="absolute top-full left-0 right-0 mt-1 text-xs text-gray-600 bg-yellow-50 px-2 py-1 rounded border">
                    Balance Packs: {balanceInfo.balancePacks}
                  </div>
                )}
              </div>

              <input 
                id="price_per_kg" 
                ref={refs.pricePerKg} 
                name="price_per_kg" 
                type="number" 
                step="0.01" 
                value={form.price_per_kg} 
                onChange={(e) => handleInputChange(e, 7)} 
                onKeyDown={(e) => handleKeyDown(e, 7)}
                onMouseDown={() => handleFieldMouseDown("price_per_kg")}
                placeholder="Price/kg" 
                className="px-4 py-2 border rounded-xl" 
              />
              <input 
                id="total" 
                ref={refs.total} 
                name="total" 
                type="number" 
                value={form.total} 
                readOnly 
                placeholder="Total" 
                onKeyDown={(e) => handleKeyDown(e, 8)}
                onMouseDown={() => handleFieldMouseDown("total")}
                className="px-4 py-2 border bg-gray-100 rounded-xl" 
              />
            </div>
          </div>

          <div className="flex space-x-4">
            <button type="submit" className="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">{editingSaleId ? "Update Sales Entry" : "Add Sales Entry"}</button>
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
                  <th className="px-4 py-2 border">Code</th>
                  <th className="px-4 py-2 border">Customer</th>
                  <th className="px-4 py-2 border">Item</th>
                  <th className="px-4 py-2 border">Weight (kg)</th>
                  <th className="px-4 py-2 border">Price</th>
                  <th className="px-4 py-2 border">Total</th>
                  <th className="px-4 py-2 border">Packs</th>
                </tr>
              </thead>
              <tbody ref={salesTableBodyRef}>
                {displayedSales.map((s, idx) => (
                  <tr
                    key={s.id || idx}
                    tabIndex={0}
                    className="text-center hover:bg-gray-50 cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-blue-100"
                    onClick={() => handleEditClick(s)}
                    onKeyDown={(e) => handleTableRowKeyDown(e, s)}
                  >
                    <td className="px-4 py-2 border">{s.code}</td>
                    <td className="px-4 py-2 border">{s.customer_code}</td>
                    <td className="px-4 py-2 border">{s.item_name}</td>
                    <td className="px-4 py-2 border">{formatDecimal(s.weight)}</td>
                    <td className="px-4 py-2 border">{formatDecimal(s.price_per_kg)}</td>
                    <td className="px-4 py-2 border">{formatDecimal(s.total)}</td>
                    <td className="px-4 py-2 border">{s.packs}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <CustomerList customers={unprintedCustomers} sales={unprintedSales} type="unprinted" searchQuery={searchQueries.unprinted} onSearchChange={(value) => setSearchQueries(prev => ({ ...prev, unprinted: value }))} />
    </div>
  );
}