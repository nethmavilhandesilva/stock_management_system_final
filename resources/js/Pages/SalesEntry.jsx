import React, { useState, useEffect, useMemo, useRef } from "react";
import Select from "react-select";

const CustomerList = React.memo(({
  customers, type, searchQuery, onSearchChange, selectedPrintedCustomer,
  selectedUnprintedCustomer, handleCustomerClick, formatDecimal, allSales
}) => (
  <div className="w-full shadow-xl rounded-xl overflow-y-auto max-h-screen border border-black" style={{ backgroundColor: "#1ec139ff" }}>
    <div style={{ backgroundColor: "#006400" }} className="p-1 rounded-t-xl">
      <h2 className="text-base font-bold text-white mb-1 whitespace-nowrap text-center">
        {type === "printed" ? "මුද්‍රණය කළ" : "මුද්‍රණය නොකළ"}
      </h2>
      <input
        type="text"
        placeholder={`Search by ${type === "printed" ? "Bill No or Code..." : "Customer Code..."}`}
        value={searchQuery}
        onChange={(e) => onSearchChange(e.target.value.toUpperCase())}
        className="w-full px-4 py-0.5 border rounded-xl focus:ring-2 focus:ring-blue-300 uppercase"
      />
    </div>

    <div className="py-1">
      {customers.length === 0 ? (
        <p className="text-gray-700">No {type === "printed" ? "printed sales" : "unprinted sales"} found.</p>
      ) : (
        <ul className="flex flex-col px-1">
          {customers.map((customerCode) => {
            const customerSales = allSales.filter(s => s.customer_code === customerCode);
            const customerTotal = customerSales.reduce((sum, sale) => sum + (parseFloat(sale.total) || 0), 0);
            const isSelected = (type === "printed" ? selectedPrintedCustomer : selectedUnprintedCustomer) === customerCode;

            return (
              <li key={customerCode} className="flex">
                <button
                  onClick={() => handleCustomerClick(type, customerCode)}
                  className={`w-full py-1 mb-2 rounded-xl border border-black text-left ${isSelected ? "bg-blue-500 text-white border-blue-600" : "bg-gray-50 hover:bg-gray-100 border-gray-200"
                    }`}
                >
                  <span className="font-semibold truncate pl-4">
                    {customerCode} - {formatDecimal(customerTotal)}
                  </span>
                </button>
              </li>
            );
          })}
        </ul>
      )}
    </div>
  </div>
));

const ItemSummary = ({ sales, formatDecimal }) => {
  const summary = useMemo(() => {
    const result = {};
    sales.forEach(sale => {
      const itemName = sale.item_name || 'Unknown';
      if (!result[itemName]) result[itemName] = { totalWeight: 0, totalPacks: 0 };
      result[itemName].totalWeight += parseFloat(sale.weight) || 0;
      result[itemName].totalPacks += parseInt(sale.packs) || 0;
    });
    return result;
  }, [sales]);

  if (Object.keys(summary).length === 0) return null;

  return (
    <div className="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
      <h3 className="text-sm font-bold text-gray-700 mb-2 text-center">Item Summary</h3>
      <div className="flex flex-wrap gap-2 justify-center">
        {Object.entries(summary).map(([itemName, data]) => (
          <div key={itemName} className="px-3 py-1 bg-white border border-gray-300 rounded-full text-xs font-medium">
            <span className="font-semibold">{itemName}:</span>
            <span className="ml-1 text-blue-600">{data.totalWeight}kg</span>
            <span className="mx-1 text-gray-400">/</span>
            <span className="text-green-600">{data.totalPacks}p</span>
          </div>
        ))}
      </div>
    </div>
  );
};

export default function SalesEntry() {
  const getInitialData = () => ({
    sales: (window.__INITIAL_SALES__ || []).filter(s => s.id),
    printed: (window.__PRINTED_SALES__ || []).filter(s => s.id),
    unprinted: (window.__UNPRINTED_SALES__ || []).filter(s => s.id),
    customers: window.__CUSTOMERS__ || [],
    entries: window.__ENTRIES__ || [],
    items: window.__ITEMS__ || [],
    storeUrl: window.__STORE_URL__ || "/grn",
    csrf: document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
    routes: window.__ROUTES__ || {}
  });

  const initialData = getInitialData();
  const refs = {
    customerCode: useRef(null), customerSelect: useRef(null), givenAmount: useRef(null),
    grnSelect: useRef(null), itemName: useRef(null), weight: useRef(null),
    pricePerKg: useRef(null), packs: useRef(null), total: useRef(null)
  };

  const fieldOrder = ["customer_code_input", "customer_code_select", "given_amount", "grn_entry_code", "item_name", "weight", "price_per_kg", "packs", "total"];
  const skipMap = { customer_code_input: "grn_entry_code", grn_entry_code: "weight" };

  const initialFormData = {
    customer_code: "", customer_name: "", supplier_code: "", code: "", item_code: "",
    item_name: "", weight: "", price_per_kg: "", pack_due: "", total: "", packs: "", grn_entry_code: "",
    original_weight: "", original_packs: "", given_amount: ""
  };

  const [state, setState] = useState({
    allSales: [...initialData.sales, ...initialData.printed, ...initialData.unprinted],
    selectedPrintedCustomer: null,
    selectedUnprintedCustomer: null,
    editingSaleId: null,
    grnSearchInput: "",
    searchQueries: { printed: "", unprinted: "" },
    errors: {},
    balanceInfo: { balancePacks: 0, balanceWeight: 0 },
    loanAmount: 0,
    isManualClear: false,
    isSubmitting: false,
    formData: initialFormData,
    packCost: 0,
    realTimeGrnEntries: initialData.entries,
    customerSearchInput: "",
    searchQueries: { printed: "", unprinted: "" },
  });

  const setFormData = (updater) => setState(prev => ({
    ...prev,
    formData: typeof updater === 'function' ? updater(prev.formData) : updater
  }));

  const updateState = (updates) => setState(prev => ({ ...prev, ...updates }));

  const { allSales, customerSearchInput, selectedPrintedCustomer, selectedUnprintedCustomer, editingSaleId, searchQueries, errors, balanceInfo, loanAmount, isManualClear, formData, packCost } = state;

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
    return sales.slice().reverse();
  }, [newSales, unprintedSales, printedSales, selectedUnprintedCustomer, selectedPrintedCustomer]);

  const autoCustomerCode = useMemo(() =>
    displayedSales.length > 0 && !isManualClear ? displayedSales[0].customer_code || "" : "",
    [displayedSales, isManualClear]
  );

  const currentBillNo = useMemo(() =>
    selectedPrintedCustomer ? printedSales.find(s => s.customer_code === selectedPrintedCustomer)?.bill_no || "N/A" : "",
    [selectedPrintedCustomer, printedSales]
  );

  const calculateTotal = (sales) => sales.reduce((acc, s) =>
    acc + (parseFloat(s.total) || parseFloat(s.weight || 0) * parseFloat(s.price_per_kg || 0) || 0), 0
  );

  const mainTotal = calculateTotal(displayedSales);
  const unprintedTotal = calculateTotal(unprintedSales);
  const formatDecimal = (val) => (Number.isFinite(parseFloat(val)) ? parseFloat(val).toFixed(2) : "0.00");

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

  const fetchLatestGrnEntries = async () => {
    try {
      const response = await fetch('/grn-entries/latest', {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': initialData.csrf
        }
      });

      const data = await response.json();

      if (data.success) {
        updateState({ realTimeGrnEntries: data.entries });
        return data.entries;
      } else {
        console.error('Failed to fetch GRN entries:', data.message);
        return state.realTimeGrnEntries; // Return current entries as fallback
      }
    } catch (error) {
      console.error('Error fetching GRN entries:', error);
      return state.realTimeGrnEntries; // Return current entries as fallback
    }
  };

  const fetchLoanAmount = async (customerCode) => {
    if (!customerCode) return updateState({ loanAmount: 0 });
    try {
      const loanResponse = await fetch(initialData.routes.getLoanAmount, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': initialData.csrf },
        body: JSON.stringify({ customer_short_name: customerCode })
      });
      const loanData = await loanResponse.json();
      updateState({ loanAmount: parseFloat(loanData.total_loan_amount) || 0 });
    } catch (loanError) {
      console.error('Error fetching loan amount:', loanError);
      updateState({ loanAmount: 0 });
    }
  };

  useEffect(() => {
    const w = parseFloat(formData.weight) || 0;
    const p = parseFloat(formData.price_per_kg) || 0;
    const packs = parseInt(formData.packs) || 0;
    const packDue = parseFloat(formData.pack_due) || 0;
    setFormData(prev => ({ ...prev, total: (w * p) + (packs * packDue) ? Number(((w * p) + (packs * packDue)).toFixed(2)) : "" }));
  }, [formData.weight, formData.price_per_kg, formData.packs, formData.pack_due]);

  useEffect(() => { refs.customerCode.current?.focus(); }, []);

  useEffect(() => {
    if (formData.grn_entry_code) {
      const matchingEntry = initialData.entries.find((en) => en.code === formData.grn_entry_code);
      updateState({ balanceInfo: matchingEntry ? { balancePacks: matchingEntry.packs || 0, balanceWeight: matchingEntry.weight || 0 } : { balancePacks: 0, balanceWeight: 0 } });
    } else updateState({ balanceInfo: { balancePacks: 0, balanceWeight: 0 } });
  }, [formData.grn_entry_code, initialData.entries]);

  const handleKeyDown = (e, currentFieldIndex) => {
    if (e.key === "Enter") {
      e.preventDefault();

      // Handle given_amount submission
      if (fieldOrder[currentFieldIndex] === "given_amount" && formData.given_amount) {
        return handleSubmitGivenAmount(e);
      }

      // Submit form when Enter is pressed in packs field
      if (fieldOrder[currentFieldIndex] === "packs") {
        return handleSubmit(e);
      }

      // For price_per_kg, move to packs instead of submitting
      if (fieldOrder[currentFieldIndex] === "price_per_kg") {
        const packsIndex = fieldOrder.findIndex(f => f === "packs");
        requestAnimationFrame(() => setTimeout(() => {
          const packsRef = Object.values(refs)[packsIndex];
          packsRef?.current?.focus?.() || packsRef?.current?.select?.();
        }, 0));
        return;
      }

      let nextIndex = currentFieldIndex + 1;
      if (skipMap[fieldOrder[currentFieldIndex]]) {
        const targetIndex = fieldOrder.findIndex(f => f === skipMap[fieldOrder[currentFieldIndex]]);
        if (targetIndex !== -1) nextIndex = targetIndex;
      }

      requestAnimationFrame(() => setTimeout(() => {
        const nextRef = Object.values(refs)[nextIndex];
        nextRef?.current?.focus?.() || nextRef?.current?.select?.();
      }, 0));
    }
  };
  const handleInputChange = (field, value) => {
    setFormData(prev => ({ ...prev, [field]: value }));

    if (field === 'customer_code') {
      const trimmedValue = value.trim();
      updateState({ isManualClear: value === '' });

      // Find matching customer from unprinted sales
      const matchingCustomer = unprintedCustomers.find(code => code.toLowerCase() === trimmedValue.toLowerCase());
      if (matchingCustomer) {
        updateState({ selectedUnprintedCustomer: matchingCustomer, selectedPrintedCustomer: null });
      } else if (selectedUnprintedCustomer) {
        updateState({ selectedUnprintedCustomer: null });
      }

      if (!trimmedValue) {
        updateState({ loanAmount: 0 });
        // Clear given amount when customer code is cleared
        setFormData(prev => ({ ...prev, given_amount: "" }));
      }

      // Find customer details
      const customer = initialData.customers.find(c => c.short_name === value);

      // FIND GIVEN AMOUNT FROM ALL SALES FOR THIS CUSTOMER
      const customerSales = allSales.filter(s => s.customer_code === trimmedValue);
      const firstSale = customerSales[0];
      const givenAmount = firstSale?.given_amount || "";

      setFormData(prev => ({
        ...prev,
        customer_name: customer?.name || "",
        given_amount: givenAmount // AUTO-POPULATE THE GIVEN AMOUNT
      }));

      fetchLoanAmount(trimmedValue);
    }

    // ... rest of your existing code for other fields
    if (field === 'grn_entry_code') {
      const grnEntry = initialData.entries.find(entry => entry.code === value);
      if (grnEntry) {
        const itemCodeToMatch = grnEntry.item_code;
        const matchingItem = initialData.items.find(i => String(i.no) === String(itemCodeToMatch));
        const fetchedPackDue = parseFloat(matchingItem?.pack_due) || 0;
        const fetchedPackCost = parseFloat(matchingItem?.pack_cost) || 0;
        setFormData(prev => ({
          ...prev,
          supplier_code: grnEntry.supplier_code,
          item_code: grnEntry.item_code,
          item_name: grnEntry.item_name || "",
          pack_due: fetchedPackDue,
        }));
        updateState({ packCost: fetchedPackCost });
      }
    }
  };

  // REPLACE THE ENTIRE OLD FUNCTION WITH THIS:
  const handleCustomerSelect = (selectedOption) => {
    // react-select passes the object { value: 'short_name', label: 'Customer Name (short_name)' }
    const short = selectedOption ? selectedOption.value : "";
    const customer = initialData.customers.find(x => String(x.short_name) === String(short));
    const hasUnprintedSales = unprintedCustomers.includes(short);

    updateState({
      selectedUnprintedCustomer: hasUnprintedSales ? short : null,
      selectedPrintedCustomer: null,
      customerSearchInput: "" // Clear search input on selection
    });

    setFormData(prev => ({
      ...prev,
      customer_code: short || "", // If no option selected (cleared), set to ""
      customer_name: customer?.name || ""
    }));

    fetchLoanAmount(short);
    updateState({ isManualClear: false });
    handleSubmitGivenAmount();

    setTimeout(() => {
      refs.grnSelect.current?.focus();
    }, 100);
  };

  const handleEditClick = (sale) => {
    // Find the GRN entry to get the item_code
    const grnEntry = initialData.entries.find(entry => entry.code === (sale.grn_entry_code || sale.code));

    let fetchedPackDue = sale.pack_due || ""; // Start with existing pack_due

    // If we found a GRN entry, look up the latest pack_due from items
    if (grnEntry && grnEntry.item_code) {
      const itemCodeToMatch = grnEntry.item_code;
      const matchingItem = initialData.items.find(i => String(i.no) === String(itemCodeToMatch));
      fetchedPackDue = parseFloat(matchingItem?.pack_due) || sale.pack_due || "";
    }

    setFormData({
      ...sale,
      grn_entry_code: sale.grn_entry_code || sale.code || "",
      item_name: sale.item_name || "",
      customer_code: sale.customer_code || "",
      customer_name: sale.customer_name || "",
      supplier_code: sale.supplier_code || "",
      item_code: sale.item_code || "",
      weight: sale.weight || "",
      price_per_kg: sale.price_per_kg || "",
      pack_due: fetchedPackDue, // Use the fetched pack_due
      total: sale.total || "",
      packs: sale.packs || "",
      original_weight: sale.original_weight || "",
      original_packs: sale.original_packs || "",
    });
    updateState({ editingSaleId: sale.id, isManualClear: false });
    setTimeout(() => { refs.weight.current?.focus(); refs.weight.current?.select(); }, 0);
  };

  const handleTableRowKeyDown = (e, sale) => {
    if (e.key === "Enter") { e.preventDefault(); handleEditClick(sale); }
  };

  const handleClearForm = () => {
    setFormData(initialFormData);
    updateState({
      editingSaleId: null,
      grnSearchInput: "",
      balanceInfo: { balancePacks: 0, balanceWeight: 0 },
      loanAmount: 0,
      isManualClear: false,
      packCost: 0,
      customerSearchInput: "",
    });
  };

  const handleDeleteClick = async () => {
    if (!editingSaleId || !window.confirm("Are you sure you want to delete this sales record?")) return;

    try {
      // Laravel RESTful delete route
      const url = `/sales/${editingSaleId}`;

      await apiCall(url, "DELETE");

      updateState({
        allSales: allSales.filter(s => s.id !== editingSaleId)
      });
      fetchLatestGrnEntries();
      handleClearForm();
    } catch (error) {
      updateState({ errors: { form: error.message } });
    }
  };
  useEffect(() => {
    // Refresh GRN data when component mounts
    fetchLatestGrnEntries();

    // Optional: Set up interval to refresh data every 30 seconds
    const interval = setInterval(fetchLatestGrnEntries, 30000);

    return () => clearInterval(interval);
  }, []);

 const handleSubmitGivenAmount = async (e) => {
  e.preventDefault();
  updateState({ errors: {} });

  // Get customer code from BOTH sources: form input AND React Select
  const customerCode = formData.customer_code || autoCustomerCode;

  if (!customerCode) {
    updateState({ errors: { form: "Please enter or select a customer code first" } });
    refs.customerCode.current?.focus();
    return;
  }

  if (!formData.given_amount) {
    updateState({ errors: { form: "Please enter a given amount" } });
    return;
  }

  const customerSales = allSales.filter(s => s.customer_code === customerCode);
  const firstSale = customerSales[0];
  if (!firstSale) {
    updateState({ errors: { form: "No sales records found for this customer. Please add a sales record first." } });
    return;
  }

  try {
    const url = window.__ROUTES__.givenAmount.replace(':id', firstSale.id);
    const data = await apiCall(url, "PUT", { given_amount: parseFloat(formData.given_amount) || 0 });

    // Update the sales data with the new accumulated given_amount
    updateState({
      allSales: allSales.map(s => s.id === data.sale.id ? data.sale : s)
    });

    // CLEAR the input field after successful submission
    setFormData(prev => ({
      ...prev,
      given_amount: "" // Clear the field instead of showing accumulated total
    }));

    refs.grnSelect.current?.focus();
  } catch (error) {
    updateState({ errors: { form: error.message } });
  }
};
  const getCurrentBalance = () => {
    if (!formData.grn_entry_code) {
      return { balancePacks: 0, balanceWeight: 0 };
    }

    const latestEntry = state.realTimeGrnEntries.find(en => en.code === formData.grn_entry_code);
    return {
      balancePacks: latestEntry?.packs || 0,
      balanceWeight: latestEntry?.weight || 0
    };
  };

  const { isSubmitting } = state;

  const handleSubmit = async (e) => {
    e.preventDefault();

    // Prevent multiple submissions
    if (isSubmitting) {
      console.log('Submission already in progress...');
      return;
    }

    updateState({ errors: {}, isSubmitting: true });

    try {
      // FIX: Use autoCustomerCode as fallback when formData.customer_code is empty
      const customerCode = formData.customer_code || autoCustomerCode;

      if (!customerCode) {
        updateState({
          errors: { form: "Customer code is required" },
          isSubmitting: false
        });
        refs.customerCode.current?.focus();
        return;
      }

      const isEditing = editingSaleId !== null;
      let billPrintedStatus = undefined;
      if (!isEditing) {
        if (selectedPrintedCustomer) billPrintedStatus = 'Y';
        else if (selectedUnprintedCustomer) billPrintedStatus = 'N';
      }

      const customerSales = allSales.filter(s => s.customer_code === customerCode);
      const isFirstRecordForCustomer = customerSales.length === 0 && !isEditing;

      const payload = {
        supplier_code: formData.supplier_code,
        customer_code: customerCode.toUpperCase(), // Use the fallback value here
        customer_name: formData.customer_name,
        code: formData.code || formData.grn_entry_code,
        item_code: formData.item_code,
        item_name: formData.item_name,
        weight: parseFloat(formData.weight) || 0,
        price_per_kg: parseFloat(formData.price_per_kg) || 0,
        pack_due: parseFloat(formData.pack_due) || 0,
        total: parseFloat(formData.total) || 0,
        packs: parseInt(formData.packs) || 0,
        grn_entry_code: formData.grn_entry_code,
        original_weight: formData.original_weight,
        original_packs: formData.original_packs,
        given_amount: (isFirstRecordForCustomer || (isEditing && customerSales[0]?.id === editingSaleId))
          ? (formData.given_amount ? parseFloat(formData.given_amount) : null)
          : null,
        ...(billPrintedStatus && { bill_printed: billPrintedStatus }),
      };

      const url = isEditing ? `/sales/${editingSaleId}` : initialData.storeUrl;
      const method = isEditing ? "PUT" : "POST";
      const data = await apiCall(url, method, payload);
      let newSale = isEditing ? data.sale : data.data || {};

      if (!newSale.grn_entry_code && formData.grn_entry_code) newSale = { ...newSale, grn_entry_code: formData.grn_entry_code };
      if (!newSale.code && formData.code) newSale = { ...newSale, code: formData.code };
      if (!isEditing && billPrintedStatus && !newSale.bill_printed) newSale = { ...newSale, bill_printed: billPrintedStatus };

      updateState({
        allSales: isEditing ? allSales.map(s => s.id === newSale.id ? newSale : s) : [...allSales, newSale]
      });

      // ✅ CALL fetchLatestGrnEntries HERE - After successful submission
      await fetchLatestGrnEntries();

      setFormData(prevForm => ({
        customer_code: prevForm.customer_code || customerCode, // Preserve the customer code
        customer_name: prevForm.customer_name,
        supplier_code: "", code: "", item_code: "", item_name: "", weight: "", price_per_kg: "", pack_due: "", total: "", packs: "",
        grn_entry_code: "", original_weight: "", original_packs: "", given_amount: ""
      }));

      updateState({
        editingSaleId: null,
        grnSearchInput: "",
        balanceInfo: { balancePacks: 0, balanceWeight: 0 },
        isManualClear: false,
        isSubmitting: false,
        packCost: 0
      });

      refs.grnSelect.current?.focus();
    } catch (error) {
      updateState({ errors: { form: error.message }, isSubmitting: false });
    }
  };

  const handleCustomerClick = async (type, customerCode) => {
    const isPrinted = type === 'printed';
    const isCurrentlySelected = isPrinted ? selectedPrintedCustomer === customerCode : selectedUnprintedCustomer === customerCode;

    if (isPrinted) {
      updateState({
        selectedPrintedCustomer: isCurrentlySelected ? null : customerCode,
        selectedUnprintedCustomer: null
      });
    } else {
      updateState({
        selectedUnprintedCustomer: isCurrentlySelected ? null : customerCode,
        selectedPrintedCustomer: null
      });
    }

    const customer = initialData.customers.find(x => String(x.short_name) === String(customerCode));
    const customerSales = allSales.filter(s => s.customer_code === customerCode);
    const firstSale = customerSales[0];
    const newCustomerCode = isCurrentlySelected ? "" : customerCode;

    // FIX: Display the existing given_amount in the input field, but don't auto-submit it
    if (!isCurrentlySelected) {
      setFormData({
        ...initialFormData, // Reset to initial form data
        customer_code: newCustomerCode,
        customer_name: customer?.name || "",
        given_amount: firstSale?.given_amount || "" // Show existing amount for reference
      });
    } else {
      // If deselecting, clear the entire form
      handleClearForm();
    }

    updateState({
      editingSaleId: null, // FIX: Clear editing state
      isManualClear: false,
      customerSearchInput: ""
    });
    fetchLoanAmount(newCustomerCode);

    // REMOVED the problematic automatic API call that was re-adding existing amount
    // The existing given_amount is now only displayed, not re-submitted

    if (isCurrentlySelected) {
      refs.customerCode.current?.focus();
      handleClearForm();
    } else {
      refs.grnSelect.current?.focus();
    }
  };
  const handleMarkPrinted = async () => {
    try { await handlePrintAndClear(); } catch (error) { alert("Mark printed failed: " + error.message); }
  };

  const handleMarkAllProcessed = async () => {
    const salesToProcess = [...newSales, ...unprintedSales];
    if (salesToProcess.length === 0) return;

    try {
      const data = await apiCall(initialData.routes.markAllProcessed, "POST", {
        sales_ids: salesToProcess.map(s => s.id)
      });

      if (data.success) {
        updateState({
          allSales: allSales.map(s =>
            salesToProcess.some(ps => ps.id === s.id)
              ? { ...s, bill_printed: "N" }
              : s
          )
        });
        handleClearForm();
        updateState({ selectedUnprintedCustomer: null, selectedPrintedCustomer: null });
        [50, 100, 150, 200, 250].forEach(timeout =>
          setTimeout(() => refs.customerCode.current?.focus(), timeout)
        );
      }
    } catch (err) {
      console.error("Failed to mark sales as processed:", err.message);
    }
  };
  const handleDeleteRecord = async (saleId) => {
    if (!saleId || !window.confirm("Are you sure you want to delete this sales record?")) return;

    try {
      const url = `/sales/${saleId}`;
      await apiCall(url, "DELETE");

      updateState({
        allSales: allSales.filter(s => s.id !== saleId)
      });
      fetchLatestGrnEntries();

      // Clear form if we were editing the deleted record
      if (editingSaleId === saleId) {
        handleClearForm();

      }
    } catch (error) {
      updateState({ errors: { form: error.message } });
    }
  };

  const getAvailableBalance = () => {
    if (!formData.grn_entry_code) {
      return { availableWeight: 0, availablePacks: 0 };
    }

    const latestEntry = state.realTimeGrnEntries.find(en => en.code === formData.grn_entry_code);
    if (!latestEntry) return { availableWeight: 0, availablePacks: 0 };

    // Get the original GRN balance (always from the database)
    const originalGrnWeight = latestEntry.weight || 0;
    const originalGrnPacks = latestEntry.packs || 0;

    // Get what's currently typed in the form
    const newWeight = parseFloat(formData.weight) || 0;
    const newPacks = parseInt(formData.packs) || 0;

    // If editing, add back the old value and subtract the new value
    if (editingSaleId) {
      const originalSale = allSales.find(s => s.id === editingSaleId);
      const oldWeight = parseFloat(originalSale?.weight) || 0;
      const oldPacks = parseInt(originalSale?.packs) || 0;

      return {
        availableWeight: originalGrnWeight + oldWeight - newWeight,
        availablePacks: originalGrnPacks + oldPacks - newPacks
      };
    } else {
      // For new entries, just subtract the new value
      return {
        availableWeight: originalGrnWeight - newWeight,
        availablePacks: originalGrnPacks - newPacks
      };
    }
  };

  const handleFullRefresh = () => { window.location.reload(); };

  const printSingleContent = async (html, customerName) => {
    return new Promise((resolve) => {
      const originalContent = document.body.innerHTML;
      document.title = customerName;

      const cleanup = () => {
        document.body.innerHTML = originalContent;
        resolve();
      };

      const tryPrint = () => {
        try {
          window.focus();
          window.print();
        } catch (err) {
          console.error("Print failed:", err);
        } finally {
          resolve();
        }
      };

      const afterPrintHandler = () => {
        window.removeEventListener("afterprint", afterPrintHandler);
        cleanup();
      };
      window.addEventListener("afterprint", afterPrintHandler);

      document.body.innerHTML = html;
      if (document.readyState === "complete") {
        tryPrint();
      } else {
        window.onload = tryPrint;
      }
      setTimeout(cleanup, 3000);
    });
  };

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
        <td style="text-align:center; padding-right:20px;">${(parseFloat(s.weight) || 0).toFixed(2)}</td>
        <td style="text-align:left;">${(parseFloat(s.price_per_kg) || 0).toFixed(2)}</td>
        <td style="text-align:right;">${((parseFloat(s.weight) || 0) * (parseFloat(s.price_per_kg) || 0)).toFixed(2)}</td>
      </tr>`;
    }).join("");

    const totalPrice = totalAmountSum;
    const totalSalesExcludingPackDue = salesData.reduce((sum, s) => sum + ((parseFloat(s.weight) || 0) * (parseFloat(s.price_per_kg) || 0)), 0);
    const totalPackDueCost = totalPrice - totalSalesExcludingPackDue;
    const givenAmount = salesData.reduce((sum, s) => sum + (parseFloat(s.given_amount) || 0), 0);
    const remaining = givenAmount - totalPrice;

    let itemSummaryHtml = '';
    const entries = Object.entries(itemGroups);
    for (let i = 0; i < entries.length; i += 2) {
      const first = entries[i], second = entries[i + 1];
      itemSummaryHtml += '<div style="display:flex; gap:0.5rem; margin-bottom:0.2rem;">';
      itemSummaryHtml += `<span style="padding:0.1rem 0.3rem;border-radius:0.5rem;background-color:#f3f4f6;font-size:0.6rem;">
        <strong>${first[0]}</strong>:${first[1].totalWeight}/${first[1].totalPacks}</span>`;
      if (second) itemSummaryHtml += `<span style="padding:0.1rem 0.3rem;border-radius:0.5rem;background-color:#f3f4f6;font-size:0.6rem;">
        <strong>${second[0]}</strong>:${second[1].totalWeight}/${second[1].totalPacks}</span>`;
      itemSummaryHtml += '</div>';
    }

    const givenAmountRow = givenAmount > 0 ? `<tr>
      <td style="width:50%;text-align:left;white-space:nowrap;"><span style="font-size:0.75rem;">දුන් මුදල: </span><span style="font-weight:bold;font-size:0.9rem;">${givenAmount.toFixed(2)}</span></td>
      <td style="width:50%;text-align:right;white-space:nowrap;font-size:1rem;"><span style="font-size:0.8rem;">ඉතිරිය: </span><span style="font-weight:bold;font-size:1.5rem;">${Math.abs(remaining).toFixed(2)}</span></td>
    </tr>` : '';

   const totalAmount = Math.abs(globalLoanAmount) + totalPrice;

    const loanRow = globalLoanAmount !== 0 ? `<tr>
  <td style="font-weight:normal;font-size:0.9rem;text-align:left; white-space: nowrap;">
  පෙර ණය: Rs. <span>
    ${globalLoanAmount < 0
        ? Math.abs(globalLoanAmount).toFixed(2)   // remove minus sign if negative
        : globalLoanAmount.toFixed(2)
      }
  </span>
</td>

  <td style="font-weight:bold;text-align:right;font-size:1.5em;">
    Rs. ${Math.abs(totalAmount).toFixed(2)}<!-- ✅ removes minus sign only -->
  </td>
</tr>` : '';



    return `<div class="receipt-container" style="width:100%;max-width:300px;margin:0 auto;padding:5px;">
      <div style="text-align:center;margin-bottom:5px;">
        <h3 style="font-size:1.8em;font-weight:bold;margin:0;"><span style="border:2px solid #000;padding:0.1em 0.3em;display:inline-block;margin-right:5px;">B32</span>TAG ට්‍රේඩර්ස්</h3>
       <p style="margin:0;font-size:0.7em; white-space: nowrap;"> අල, ෆී ළූනු, කුළුබඩු තොග ගෙන්වන්නෝ/බෙදාහරින්නෝ</p>
        <p style="margin:0;font-size:0.7em;">වි.ආ.ම. වේයන්ගොඩ</p>
      </div>
      <div style="text-align:left;margin-bottom:5px;">
        <table style="width:100%;font-size:9px;border-collapse:collapse;">
          <tr><td style="width:50%;">දිනය : ${date}</td><td style="width:50%;text-align:right;">${time}</td></tr>
          <tr><td colspan="2">දුර : ${mobile || ''}</td></tr>
          <tr><td>බිල් අංකය : <strong>${billNo}</strong></td><td style="text-align:right;"><strong style="font-size:2.0em;">${customerName.toUpperCase()}</strong></td></tr>
        </table>
      </div>
      <hr style="border:0.5px solid #000;margin:5px 0;">
      <table style="width:100%;font-size:9px;border-collapse:collapse;">
        <thead style="font-size:1.5em;">
          <tr><th style="text-align:left;padding:2px;">වර්ගය<br>මලු</th><th style="padding:2px;">කිලෝ</th><th style="padding:2px;">මිල</th><th style="text-align:right;padding:2px;">අගය</th></tr>
        </thead>
        <tbody>
          <tr><td colspan="4"> <hr style="border:0.5px solid #000;margin:5px 0;"></td></tr>
          ${itemsHtml}
          <tr><td colspan="4"><hr style="border:0.5px solid #000;margin:5px 0;"></td></tr>
          <tr><td colspan="2" style="text-align:left;font-weight:bold;font-size:1.2em;">${totalPacksSum}</td><td colspan="2" style="text-align:right;font-weight:bold;font-size:1.2em;">${totalSalesExcludingPackDue.toFixed(2)}</td></tr>
        </tbody>
      </table>
      <table style="width:100%;font-size:11px;border-collapse:collapse;">
        <tr><td>ප්‍රවාහන ගාස්තු:</td><td style="text-align:right;font-weight:bold;">00</td></tr>
        <tr><td>කුලිය:</td><td style="text-align:right;font-weight:bold;">${totalPackDueCost.toFixed(2)}</td></tr>
        <tr><td>අගය:</td><td style="text-align:right;font-weight:bold;"><span style="display:inline-block; border-top:1px solid #000; border-bottom:3px double #000; padding:2px 4px; min-width:80px; text-align:right; font-size:1.5em;">${(totalPrice).toFixed(2)}</span></td></tr>
        ${givenAmountRow}${loanRow}
      </table>
      <div style="font-size:10px;">${itemSummaryHtml}</div>
       <hr style="border:0.5px solid #000;margin:5px 0;">
      <div style="text-align:center;margin-top:10px;font-size:10px;">
        <p style="margin:0;">භාණ්ඩ පරීක්ෂාකර බලා රැගෙන යන්න</p><p style="margin:0;">නැවත භාර ගනු නොලැබේ</p>
      </div>
    </div>`;
  };

  const handlePrintAndClear = async () => {
    const salesData = displayedSales.filter(s => s.id);
    if (!salesData.length) return alert("No sales records to print!");

    try {
      const [printResponse, loanResponse] = await Promise.allSettled([
        apiCall(initialData.routes.markPrinted, "POST", { sales_ids: salesData.map(s => s.id) }),
        fetch(initialData.routes.getLoanAmount, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': initialData.csrf },
          body: JSON.stringify({ customer_short_name: salesData[0].customer_code || "N/A" })
        }).then(res => res.json())
      ]);

      if (printResponse.status === 'rejected' || printResponse.value.status !== "success") {
        throw new Error(printResponse.value?.message || "Printing failed");
      }

      const customerCode = salesData[0].customer_code || "N/A";
      const customerName = customerCode;
      const mobile = salesData[0].mobile || '0773358518';
      const billNo = printResponse.value.bill_no || "";

      let globalLoanAmount = 0;
      if (loanResponse.status === 'fulfilled') {
        globalLoanAmount = parseFloat(loanResponse.value.total_loan_amount) || 0;
      }

      const receiptHtml = buildFullReceiptHTML(salesData, billNo, customerName, mobile, globalLoanAmount);
      const copyHtml = `<div style="text-align:center;font-size:2em;font-weight:bold;color:red;margin-bottom:10px;">COPY</div>${receiptHtml}`;

      const printPromises = [
        printSingleContent(receiptHtml, customerName),
      ];

      await Promise.all(printPromises);
      window.location.reload();

      updateState({
        allSales: allSales.map(s => {
          const isPrinted = salesData.some(d => d.id === s.id);
          return isPrinted ? { ...s, bill_printed: 'Y', bill_no: billNo } : s;
        }),
        selectedUnprintedCustomer: null,
        selectedPrintedCustomer: null,
        customerSearchInput: ""
      });
      handleClearForm();
    } catch (error) {
      alert("Printing failed: " + error.message);
      setTimeout(() => { window.location.reload(); }, 100);
    }
  };

  const handleCustomerCodeChange = (e) => {
    const code = e.target.value;
    const customer = initialData.customers.find(x => String(x.short_name) === String(code));
    const customerSale = allSales.find(s => s.customer_code === code);
    if (!code) {
      setFormData(prev => ({ ...prev, customer_code: "", customer_name: "", given_amount: "" }));
      updateState({ selectedPrintedCustomer: null, selectedUnprintedCustomer: null });
      fetchLoanAmount("");
    } else {
      setFormData(prev => ({ ...prev, customer_code: code, customer_name: customer?.name || "", given_amount: customerSale?.given_amount || "" }));
      fetchLoanAmount(code);
    }
  };

  useEffect(() => {
    const handleShortcut = (e) => {
      if (e.key === "F1") {
        e.preventDefault(); handlePrintAndClear().finally(() => {
          [100, 200, 300, 500, 800].forEach(timeout => setTimeout(() => refs.customerCode.current?.focus(), timeout));
        });
      } else if (e.key === "F5") { e.preventDefault(); handleMarkAllProcessed(); }
    };
    window.addEventListener("keydown", handleShortcut);
    return () => window.removeEventListener("keydown", handleShortcut);
  }, [displayedSales, newSales]);

  return (
    <div className="min-h-screen flex flex-row p-4 pt-0 -mt-4" style={{ backgroundColor: "#99ff99" }}>
      <div className="w-1/3 sticky top-0 h-screen overflow-y-auto pr-2 ml-[-30px]">
        <CustomerList customers={printedCustomers} type="printed" searchQuery={searchQueries.printed}
          onSearchChange={(value) => updateState({ searchQueries: { ...searchQueries, printed: value } })}
          selectedPrintedCustomer={selectedPrintedCustomer} selectedUnprintedCustomer={selectedUnprintedCustomer}
          handleCustomerClick={handleCustomerClick} unprintedTotal={unprintedTotal} formatDecimal={formatDecimal} allSales={allSales} />
      </div>

      <div className="w-[138%] shadow-2xl rounded-3xl p-6" style={{ backgroundColor: "#111439ff" }}>
        <form onSubmit={handleSubmit} className="space-y-4">
          <div className="flex justify-between items-center bg-gray-50 p-0.5 rounded-xl shadow-sm border border-black">
            <span className="text-gray-600 font-medium">Bill No: {currentBillNo}</span>
            <h2 className="text-2xl font-bold text-red-600">Total Sales: Rs. {formatDecimal(mainTotal)}</h2>
          </div>

          <div className="grid grid-cols-1 gap-4">
            <div className="grid grid-cols-3 gap-4">
              <input id="customer_code_input" ref={refs.customerCode} name="customer_code" value={formData.customer_code || autoCustomerCode} onChange={(e) => { const value = e.target.value.toUpperCase(); handleInputChange("customer_code", value); if (value.trim() === "") { setFormData(prev => ({ ...prev, customer_code: "", customer_name: "", given_amount: "" })); updateState({ selectedPrintedCustomer: null, selectedUnprintedCustomer: null }); } }} onKeyDown={(e) => handleKeyDown(e, 0)} type="text" maxLength={10} placeholder="Customer Code" className="px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300 uppercase" />
             <Select id="customer_code_select" ref={refs.customerSelect} value={formData.customer_code ? { value: formData.customer_code, label: `${formData.customer_code}` } : null} onChange={handleCustomerSelect} options={initialData.customers.filter(c => !customerSearchInput || c.short_name.charAt(0).toUpperCase() === customerSearchInput.charAt(0).toUpperCase()).map(c => ({ value: c.short_name, label: `${c.short_name}` }))} onInputChange={(inputValue, { action }) => { if(action === "input-change") updateState({ customerSearchInput: inputValue.toUpperCase() }); }} inputValue={customerSearchInput} placeholder="-- Select Customer --" isClearable isSearchable className="rounded-xl" styles={{ control: base => ({ ...base, minHeight:"44px", height:"44px", borderRadius:"0.75rem" }), valueContainer: base => ({ ...base, padding:"0 8px", height:"44px", flex:1, display:"flex", alignItems:"center", overflow:"hidden" }), placeholder: base => ({ ...base, fontSize:"1rem" }) }} />
             <input type="text" readOnly value={`Loan: Rs. ${loanAmount < 0 ? formatDecimal(Math.abs(loanAmount)) : formatDecimal(loanAmount)}`} placeholder="Loan Amount" className="px-4 py-2 border rounded-xl bg-yellow-100 text-red-600 font-bold" />
            </div>

            <Select
              id="grn_entry_code"
              ref={refs.grnSelect}
              value={formData.grn_entry_code ? {
                value: formData.grn_entry_code,
                label: formData.grn_entry_code,
                data: state.realTimeGrnEntries.find((en) => en.code === formData.grn_entry_code)
              } : null}
              onChange={async (selected) => {
                if (selected?.data) {
                  // Fetch latest data before processing selection
                  const latestEntries = await fetchLatestGrnEntries();
                  const latestEntry = latestEntries.find(en => en.code === selected.value);

                  if (latestEntry) {
                    const matchingItem = initialData.items.find(i => String(i.no) === String(latestEntry.item_code));
                    const fetchedPackDue = parseFloat(matchingItem?.pack_due) || 0;
                    const fetchedPackCost = parseFloat(matchingItem?.pack_cost) || 0;

                    setFormData(prev => ({
                      ...prev,
                      grn_entry_code: selected.value,
                      code: selected.value,
                      item_name: latestEntry.item_name || "",
                      supplier_code: latestEntry.supplier_code || "",
                      item_code: latestEntry.item_code || "",
                      price_per_kg: latestEntry.price_per_kg || latestEntry.PerKGPrice || latestEntry.SalesKGPrice || "",
                      pack_due: fetchedPackDue,
                      weight: editingSaleId ? prev.weight : "",
                      packs: editingSaleId ? prev.packs : "",
                      total: editingSaleId ? prev.total : ""
                    }));

                    // Update balance info with real-time data
                    updateState({
                      grnSearchInput: "",
                      packCost: fetchedPackCost,
                      balanceInfo: {
                        balancePacks: latestEntry.packs || 0,
                        balanceWeight: latestEntry.weight || 0
                      }
                    });

                    requestAnimationFrame(() => setTimeout(() => refs.weight.current?.focus(), 10));
                  }
                }
              }}
              onInputChange={(inputValue, { action }) => {
                if (action === "input-change") {
                  const upperValue = inputValue.toUpperCase();
                  updateState({ grnSearchInput: upperValue });
                  return upperValue;
                }
                return inputValue;
              }}
              onKeyDown={async (e) => {
                if (e.key === "Enter" && !e.isPropagationStopped()) {
                  // Refresh data when Enter is pressed in search
                  await fetchLatestGrnEntries();
                  setTimeout(() => {
                    refs.weight.current?.focus();
                  }, 10);
                }
              }}
              getOptionLabel={(option) => option.label}
              getOptionValue={(option) => option.value}
              options={state.realTimeGrnEntries
                .filter(entry => {
                  if (!state.grnSearchInput || state.grnSearchInput === '') return false;
                  return state.grnSearchInput.charAt(0) === (entry.code ? entry.code.charAt(0) : '');
                })
                .map((en, index) => ({ value: en.code, label: en.code, data: en, index }))}
              placeholder="Select GRN Entry"
              isSearchable={true}
              noOptionsMessage={() =>
                !state.grnSearchInput || state.grnSearchInput === ''
                  ? "Type a letter to search GRN entries"
                  : "No GRN entries found matching the first letter"
              }
              formatOptionLabel={(option, { context }) => {
                const entry = option.data || state.realTimeGrnEntries.find(en => en.code === option.value);
                if (!entry) return option.label;

                const showHeader = option.index === 0;
                return (
                  <div className="w-full">
                    {showHeader && (
                      <div className="grid grid-cols-[120px_150px_55px_70px_55px_70px_90px] gap-1 px-2 py-1.5 bg-gray-100 font-bold text-xs border-b border-gray-300 items-center">
                        <div className="text-left">Code</div>
                        <div className="text-left">Item Name</div>
                        <div className="text-center">OP</div>
                        <div className="text-center">OW</div>
                        <div className="text-center">BP</div>
                        <div className="text-center">BW</div>
                        <div className="text-right">PRICE</div>
                      </div>
                    )}
                    <div className="grid grid-cols-[120px_150px_55px_70px_55px_70px_90px] gap-1 px-2 py-1 text-sm border-b border-gray-100 hover:bg-blue-200 items-center">
                      <div className="text-left font-medium text-blue-700 truncate" title={entry.code || "-"}>{entry.code || "-"}</div>
                      <div className="text-left truncate" title={entry.item_name || "Unknown Item"}>{entry.item_name || "Unknown Item"}</div>
                      <div className="text-center">{entry.original_packs || "0"}</div>
                      <div className="text-center">{formatDecimal(entry.original_weight)}</div>
                      <div className="text-center">{entry.packs || "0"}</div>
                      <div className="text-center">{formatDecimal(entry.weight)}</div>
                      <div className="text-right font-semibold text-green-600">Rs. {formatDecimal(entry.price_per_kg || entry.PerKGPrice || entry.SalesKGPrice)}</div>
                    </div>
                  </div>
                );
              }}
              components={{
                SingleValue: ({ data }) => {
                  const entry = data.data;
                  if (!entry) return data.label;
                  return (
                    <span>
                      {data.label} - {entry.item_name || "Unknown Item"} (
                      <strong>Price:</strong> Rs.{formatDecimal(entry.price_per_kg || entry.PerKGPrice || entry.SalesKGPrice)} /
                      <strong>BW:</strong> {formatDecimal(entry.weight)} /
                      <strong>BP:</strong> {entry.packs || 0})
                    </span>
                  );
                }
              }}
              styles={{
                // FIX: Corrects highlighting on hover
                option: (base, { isFocused, isSelected }) => ({
                  ...base,
                  backgroundColor: isFocused
                    ? "#bfdbfe"
                    : (isSelected ? "#e5e5e5" : "transparent"),
                  padding: 0,
                }),
                menu: (base) => ({ ...base, width: "680px", maxWidth: "95vw" }),
                menuList: (base) => ({ ...base, padding: 0, maxHeight: "400px" }),

                // FIXES: Alignment and Opacity
                control: (base, state) => ({
                  ...base,
                  minHeight: "44px",
                  height: "44px",
                  opacity: 1, // Redundant safety, but doesn't hurt
                  display: 'flex', // Ensure control is a flex container for alignment
                  alignItems: 'center',
                  flexWrap: 'nowrap', // Prevent content wrapping if needed
                }),
                valueContainer: (base) => ({
                  ...base,
                  padding: '0 8px',
                  height: '44px',
                  flex: 1,
                  display: 'flex',
                  alignItems: 'center',
                  overflow: 'hidden',
                }),
                singleValue: (base) => ({
                  ...base,
                  display: 'flex',
                  alignItems: 'center',
                  margin: 0,
                  padding: 0,
                  lineHeight: 'normal',
                  opacity: 1, // CRITICAL FIX 1: Ensures selected text is full color
                }),
                input: (base) => ({
                  ...base,
                  opacity: 1, // CRITICAL FIX 2: Ensures the search input area (which contains singleValue) is full color
                }),
                indicatorsContainer: (base) => ({
                  ...base,
                  height: '44px',
                  alignItems: 'center',
                }),
              }}
            />
            <div className="flex items-start gap-3">
              <div className="flex flex-col">
                <input id="item_name" ref={refs.itemName} type="text" value={formData.item_name} readOnly placeholder="අයිතමයේ නාමය" onKeyDown={(e) => handleKeyDown(e, 4)} className="px-4 py-3 border border-gray-400 rounded-xl text-lg font-semibold text-black w-45 bg-gray-100 overflow-x-auto whitespace-nowrap" />
                {formData.grn_entry_code && (
                  <span className="text-red-600 font-bold text-[19px] mt-1 text-center whitespace-nowrap inline-block">
                    <strong>BW:</strong> {formatDecimal(Math.max(0, getAvailableBalance().availableWeight))}
                  </span>
                )}
              </div>
              <div className="flex flex-col">
                <input id="weight" ref={refs.weight} name="weight" type="text" value={formData.weight} onChange={(e) => handleInputChange('weight', e.target.value)} onKeyDown={(e) => handleKeyDown(e, 5)} placeholder="බර" className="px-3 py-3 border border-gray-400 rounded-3xl text-right text-lg font-semibold text-black overflow-x-auto whitespace-nowrap w-[120px]" maxLength="7" />
                <span className="text-sm mt-1 text-center invisible">Placeholder</span>
              </div>
              <div className="flex flex-col">
                <input id="price_per_kg" ref={refs.pricePerKg} name="price_per_kg" type="text" value={formData.price_per_kg} onChange={(e) => handleInputChange('price_per_kg', e.target.value)} onKeyDown={(e) => handleKeyDown(e, 6)} placeholder="මිල" className="px-3 py-3 border border-gray-400 rounded-3xl text-right text-lg font-semibold text-black overflow-x-auto whitespace-nowrap w-[120px]" maxLength="7" />
                <span className="text-red-600 font-bold text-[18px] mt-1 text-center whitespace-nowrap inline-block">
                  {formatDecimal(packCost)}
                </span>
              </div>
              <div className="flex flex-col">
                <input id="packs" ref={refs.packs} name="packs" type="text" value={formData.packs} onChange={(e) => handleInputChange('packs', e.target.value)} onKeyDown={(e) => handleKeyDown(e, 7)} placeholder="අසුරුම්" className="px-3 py-3 border border-gray-400 rounded-2xl text-right text-lg font-semibold text-black w-20 overflow-x-auto whitespace-nowrap" maxLength="4" />
                {formData.grn_entry_code && (
                  <span className="text-red-600 font-bold text-[18px] mt-1 text-center whitespace-nowrap inline-block">
                    <strong>BP:</strong> {Math.max(0, getAvailableBalance().availablePacks)}
                  </span>
                )}
              </div>
              <div className="flex flex-col">
                <input id="total" ref={refs.total} name="total" type="number" value={formData.total} readOnly placeholder="Total" onKeyDown={(e) => handleKeyDown(e, 8)} onInput={(e) => e.target.value.length > 6 && (e.target.value = e.target.value.slice(0, 6))} className="px-4 py-3 border border-gray-400 rounded-xl text-right text-lg font-semibold text-black bg-gray-100 w-[10.0rem] overflow-x-auto whitespace-nowrap" maxLength="20" />
              </div>
            </div>
          </div>
          <div className="flex space-x-4">
            <button type="submit" style={{ display: "none" }} className="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
              {editingSaleId ? "Update Sales Entry" : "Add Sales Entry"}</button>
            <button type="button" onClick={handleClearForm} className="hidden py-3 px-6 bg-gray-400 hover:bg-gray-500 text-white font-bold rounded-xl shadow-lg transition">Clear</button>
          </div>
        </form>

        {errors.form && <div className="mt-6 p-3 bg-red-100 text-red-700 rounded-xl">{errors.form}</div>}

        <div className="mt-6">
          <div className="overflow-x-auto">
            <table className="min-w-full border border-gray-200 rounded-xl text-sm">
              <thead className="bg-gray-100">
                <tr><th className="px-4 py-2 border">කේතය</th><th className="px-4 py-2 border">අයිතමය</th><th className="px-2 py-2 border w-20">බර(kg)</th><th className="px-2 py-2 border w-20">මිල</th><th className="px-2 py-2 border w-24">සමස්ත</th><th className="px-2 py-2 border w-16">මලු</th><th className="px-2 py-2 border w-16">Actions</th></tr>
              </thead>
              <tbody className="bg-black text-white">
                {displayedSales.map((s, idx) => (
                  <tr key={s.id || idx} tabIndex={0} className="text-center cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-blue-100" onClick={() => handleEditClick(s)} onKeyDown={(e) => handleTableRowKeyDown(e, s)}><td className="px-4 py-2 border">{s.code}</td><td className="px-4 py-2 border">{s.item_name}</td><td className="px-2 py-2 border w-20">{formatDecimal(s.weight)}</td><td className="px-2 py-2 border w-20">{formatDecimal(s.price_per_kg)}</td><td className="px-2 py-2 border w-24">{formatDecimal((parseFloat(s.weight) || 0) * (parseFloat(s.price_per_kg) || 0))}</td><td className="px-2 py-2 border w-16">{s.packs}</td><td className="px-2 py-2 border w-16"><button onClick={(e) => { e.stopPropagation(); handleDeleteRecord(s.id); }} className="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-100 transition-colors" title="Delete record"><svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fillRule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clipRule="evenodd" /></svg></button></td></tr>
                ))}
              </tbody>
            </table>

            <ItemSummary sales={displayedSales} formatDecimal={formatDecimal} />
            <div className="flex items-center justify-between mt-6 mb-4">
              <h2 className="text-2xl font-bold text-red-600">Total Sales: Rs. {formatDecimal(mainTotal)}</h2>
              <input id="given_amount" ref={refs.givenAmount} name="given_amount" type="number" step="0.01" value={formData.given_amount}
                onChange={(e) => handleInputChange('given_amount', e.target.value)} onKeyDown={(e) => handleKeyDown(e, 2)} placeholder="Given Amount"
                className="px-4 py-2 border rounded-xl text-right w-40" />
            </div>
          </div>
        </div>

        <div className="flex justify-between items-center mt-6">
          <div className="flex space-x-3">
            <button type="button" onClick={handleMarkPrinted} className="px-4 py-1 text-sm bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl shadow transition">
              PRINT</button>
            <button type="button" onClick={handleMarkAllProcessed} className="px-4 py-1 text-sm bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow transition">
              HOLD</button>
            <button type="button" onClick={handleFullRefresh} className="px-4 py-1 text-sm bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-xl shadow transition">
              Full Refresh</button>
          </div>
        </div>
      </div>

      <div className="w-1/3 sticky top-0 h-screen overflow-y-auto pl-2 mr-[-30px]">
        <CustomerList customers={unprintedCustomers} type="unprinted" searchQuery={searchQueries.unprinted}
          onSearchChange={(value) => updateState({ searchQueries: { ...searchQueries, unprinted: value } })}
          selectedPrintedCustomer={selectedPrintedCustomer} selectedUnprintedCustomer={selectedUnprintedCustomer}
          handleCustomerClick={handleCustomerClick} unprintedTotal={unprintedTotal} formatDecimal={formatDecimal} allSales={allSales} />
      </div>
    </div>
  );
}