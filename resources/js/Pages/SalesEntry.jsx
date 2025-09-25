import React, { useState, useEffect, useMemo, useRef } from "react";

export default function SalesEntry() {
  const initialSales = window.__INITIAL_SALES__ || [];
  const initialPrintedSales = window.__PRINTED_SALES__ || [];
  const initialUnprintedSales = window.__UNPRINTED_SALES__ || [];
  const customers = window.__CUSTOMERS__ || [];
  const entries = window.__ENTRIES__ || [];
  const STORE_URL = window.__STORE_URL__ || "/grn";
  const CSRF_TOKEN =
    document.querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content") || "";

  // --- Refs for form fields ---
  const customerCodeRef = useRef(null);
  const customerSelectRef = useRef(null);
  const grnSelectRef = useRef(null);
  const itemNameRef = useRef(null);
  const weightRef = useRef(null);
  const packsRef = useRef(null);
  const pricePerKgRef = useRef(null);
  const totalRef = useRef(null);

  // --- State: Single source of truth for all sales ---
  const [allSales, setAllSales] = useState([
    ...initialSales,
    ...initialPrintedSales,
    ...initialUnprintedSales,
  ]);
  const [selectedPrintedCustomer, setSelectedPrintedCustomer] = useState(null);
  const [selectedUnprintedCustomer, setSelectedUnprintedCustomer] =
    useState(null);
  const [editingSaleId, setEditingSaleId] = useState(null);
  const [form, setForm] = useState({
    customer_code: "",
    customer_name: "",
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
    given_amount: "",
  });
  const [errors, setErrors] = useState({});
  const [grnPriceDisplay, setGrnPriceDisplay] = useState("");
  // --- New state for the search query ---
  const [unprintedSearchQuery, setUnprintedSearchQuery] = useState("");
  const [printedSearchQuery, setPrintedSearchQuery] = useState("");

  // --- Field order for navigation ---
  const fieldOrder = [
    { ref: customerCodeRef, name: 'customer_code' },
    { ref: customerSelectRef, name: 'customer_code' },
    { ref: grnSelectRef, name: 'grn_entry_code' },
    { ref: itemNameRef, name: 'item_name' },
    { ref: weightRef, name: 'weight' },
    { ref: packsRef, name: 'packs' },
    { ref: pricePerKgRef, name: 'price_per_kg' },
    { ref: totalRef, name: 'total' },
  ];

  // --- Handle Enter key navigation ---
  const handleKeyDown = (e, currentFieldIndex) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      
      // If we're on the last field (price_per_kg), submit the form
      if (currentFieldIndex === 6) { // price_per_kg is index 6
        handleSubmit(e);
        return;
      }
      
      // Move to next field
      const nextFieldIndex = currentFieldIndex + 1;
      if (nextFieldIndex < fieldOrder.length) {
        fieldOrder[nextFieldIndex].ref.current?.focus();
      }
    }
  };

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

  // --- Derived State using useMemo for efficient filtering ---
  const newSales = useMemo(() => allSales.filter((s) => s.id && !s.bill_printed), [allSales]);
  const printedSales = useMemo(() => allSales.filter((s) => s.bill_printed === 'Y'), [allSales]);
  // MODIFIED: Filter for sales explicitly marked as 'N', not just anything that isn't 'Y'.
  const unprintedSales = useMemo(() => allSales.filter((s) => s.bill_printed === 'N'), [allSales]);

  const printedCustomers = useMemo(() => {
    const allPrinted = [...new Set(printedSales.map((s) => s.customer_code))];
    if (!printedSearchQuery) {
      return allPrinted;
    }
    const lowerCaseQuery = printedSearchQuery.toLowerCase();
    const filteredByBillNo = printedSales.filter((s) =>
      (s.bill_no?.toString() || '').toLowerCase().includes(lowerCaseQuery)
    ).map((s) => s.customer_code);

    const filteredByCustomerCode = allPrinted.filter((code) =>
      code.toLowerCase().includes(lowerCaseQuery)
    );

    // Combine and get unique customer codes
    return [...new Set([...filteredByBillNo, ...filteredByCustomerCode])];
  }, [printedSales, printedSearchQuery]);


  // --- Updated useMemo to filter customers based on search query ---
  const unprintedCustomers = useMemo(() => {
    const allUnprinted = [...new Set(unprintedSales.map((s) => s.customer_code))];
    if (!unprintedSearchQuery) {
      return allUnprinted;
    }
    const lowerCaseQuery = unprintedSearchQuery.toLowerCase();
    return allUnprinted.filter((code) =>
      code.toLowerCase().includes(lowerCaseQuery)
    );
  }, [unprintedSales, unprintedSearchQuery]);

  // MODIFIED: Change the display logic to prioritize unprinted customers, then new sales, and finally printed.
  const displayedSales = useMemo(() => {
    let salesToShow = newSales;

    if (selectedUnprintedCustomer) {
      const selectedSales = unprintedSales.filter(
        (s) => s.customer_code === selectedUnprintedCustomer
      );
      // Combine new sales with the selected unprinted sales
      salesToShow = [...salesToShow, ...selectedSales];
    } else if (selectedPrintedCustomer) {
      const selectedSales = printedSales.filter(
        (s) => s.customer_code === selectedPrintedCustomer
      );
      // Combine new sales with the selected printed sales
      salesToShow = [...salesToShow, ...selectedSales];
    }

    return salesToShow;
  }, [newSales, unprintedSales, printedSales, selectedUnprintedCustomer, selectedPrintedCustomer]);

  // --- Derived State for Bill No ---
  const currentBillNo = useMemo(() => {
    if (selectedPrintedCustomer) {
      const firstPrintedSale = printedSales.find(
        (s) => s.customer_code === selectedPrintedCustomer
      );
      return firstPrintedSale?.bill_no || "N/A";
    }
    return "";
  }, [selectedPrintedCustomer, printedSales]);

  // --- Form handlers ---
  function handleCustomerSelect(e) {
    const short = e.target.value;
    const c = customers.find((x) => String(x.short_name) === String(short));
    setForm((prev) => ({
      ...prev,
      customer_code: short || prev.customer_code,
      customer_name: c?.name || "",
    }));
  }

  function handleGrnSelect(e) {
    const code = e.target.value;
    const entry = entries.find((x) => String(x.code) === String(code));
    if (entry) {
      setForm((prev) => ({
        ...prev,
        grn_entry_code: code,
        supplier_code: entry.supplier_code || prev.supplier_code || "",
        code: entry.code || prev.code || "",
        item_code: entry.item_code || entry.itemCode || prev.item_code || "",
        item_name: entry.item_name || entry.itemName || prev.item_name || "",
        price_per_kg:
          entry.price_per_kg ??
          entry.PerKGPrice ??
          entry.SalesKGPrice ??
          prev.price_per_kg ??
          "",
        original_weight: entry.original_weight ?? prev.original_weight ?? "",
        original_packs: entry.original_packs ?? prev.original_packs ?? "",
        given_amount: entry.given_amount ?? prev.given_amount ?? "",
      }));
      setGrnPriceDisplay(
        entry.price_per_kg ?? entry.PerKGPrice ?? entry.SalesKGPrice ?? ""
      );
    } else {
      setForm((prev) => ({
        ...prev,
        grn_entry_code: "",
        code: "",
        item_code: "",
        item_name: "",
        price_per_kg: "",
        original_weight: "",
        original_packs: "",
        given_amount: "",
      }));
      setGrnPriceDisplay("");
    }
  }

  function handleEditClick(sale) {
    setForm({
      customer_code: sale.customer_code,
      customer_name: sale.customer_name,
      supplier_code: sale.supplier_code,
      code: sale.code,
      item_code: sale.item_code,
      item_name: sale.item_name,
      weight: sale.weight,
      price_per_kg: sale.price_per_kg,
      total: sale.total,
      packs: sale.packs,
      grn_entry_code: sale.grn_entry_code,
      original_weight: sale.original_weight,
      original_packs: sale.original_packs,
      given_amount: sale.given_amount,
    });
    setEditingSaleId(sale.id);
  }

  function handleClearForm() {
    setForm({
      customer_code: "",
      customer_name: "",
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
      given_amount: "",
    });
    setEditingSaleId(null);
    setGrnPriceDisplay("");
  }

  // --- Updated Delete handler to use single state ---
  async function handleDeleteClick() {
    if (!editingSaleId) return;

    if (!window.confirm("Are you sure you want to delete this sales record?")) {
      return;
    }

    try {
      const res = await fetch(`/sales/${editingSaleId}`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": CSRF_TOKEN,
        },
      });

      const data = await res.json();

      if (!res.ok) {
        setErrors({ form: data.error || "Failed to delete record." });
        return;
      }

      setAllSales((prevSales) =>
        prevSales.filter((s) => s.id !== editingSaleId)
      );

      handleClearForm();

      alert(data.message || "Record deleted successfully.");
    } catch (err) {
      setErrors({ form: err.message || "Network or server error" });
    }
  }

  // --- Calculate total (unchanged) ---
  useEffect(() => {
    const w = parseFloat(form.weight) || 0;
    const p = parseFloat(form.price_per_kg) || 0;
    const tot = w * p;
    setForm((prev) => ({
      ...prev,
      total: tot ? Number(tot.toFixed(2)) : "",
    }));
  }, [form.weight, form.price_per_kg]);

  // --- Updated Submit handler to use single state ---
  async function handleSubmit(e) {
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
      const url = isEditing ? `/sales/${editingSaleId}` : STORE_URL;
      const method = isEditing ? "PUT" : "POST";

      const res = await fetch(url, {
        method: method,
        headers: {
          "Content-Type": "application/json",
          Accept: "application/json",
          "X-CSRF-TOKEN": CSRF_TOKEN,
        },
        body: JSON.stringify(payload),
      });

      const data = await res.json().catch(() => ({}));

      if (!res.ok) {
        if (res.status === 422 && data.errors) {
          setErrors(data.errors);
        } else {
          setErrors({ form: data.error || "Server error" });
        }
        return;
      }

      if (isEditing) {
        const updatedSale = data.sale;
        setAllSales((prev) =>
          prev.map((s) => (s.id === updatedSale.id ? updatedSale : s))
        );
      } else {
        const newSale = data.data || {};
        setAllSales((prev) => [...prev, newSale]);
      }

      handleClearForm();
      // Focus back to customer code field after submission
      customerCodeRef.current?.focus();
    } catch (err) {
      setErrors({ form: err.message || "Network or server error" });
    }
  }

  function formatDecimal(val) {
    const n = parseFloat(val);
    return Number.isFinite(n) ? n.toFixed(2) : "0.00";
  }

  const mainTotal = displayedSales.reduce((acc, s) => {
    const t =
      parseFloat(s.total) ||
      parseFloat(s.weight || 0) * parseFloat(s.price_per_kg || 0);
    return acc + (isNaN(t) ? 0 : t);
  }, 0);

  const unprintedTotal = unprintedSales.reduce((acc, s) => {
    const t =
      parseFloat(s.total) ||
      parseFloat(s.weight || 0) * parseFloat(s.price_per_kg || 0);
    return acc + (isNaN(t) ? 0 : t);
  }, 0);

  // --- F1: Print & Clear (unchanged, but uses the new state structure) ---
  useEffect(() => {
    function handleKeyDown(e) {
      if (e.key === "F1") {
        e.preventDefault();
        handlePrintAndClear();
      }
    }
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [displayedSales, unprintedSales, newSales]);

  // --- F5: Mark all processed (updated to use the new state) ---
  useEffect(() => {
    function handleKeyDown(e) {
      if (e.key === "F5") {
        e.preventDefault();
        markAllSalesAsProcessed();
      }
    }
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [newSales]);

  async function markAllSalesAsProcessed() {
    const salesToProcess = newSales.map((s) => s.id);
    if (!salesToProcess.length) {
      alert("No new sales to process.");
      return;
    }
    if (!window.confirm("Are you sure you want to mark ALL sales as processed?"))
      return;
    try {
      const res = await fetch("/sales/mark-all-processed", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": CSRF_TOKEN,
        },
        body: JSON.stringify({ sales_ids: salesToProcess }),
      });
      const data = await res.json();
      if (data.success) {
        alert(data.message || "All sales marked as processed successfully!");
        setAllSales((prevSales) =>
          prevSales.map((s) =>
            salesToProcess.includes(s.id)
              ? { ...s, bill_printed: "N" }
              : s
          )
        );
      } else {
        alert(data.message || "Failed to mark sales as processed.");
      }
    } catch (err) {
      console.error("Error marking sales as processed:", err);
      alert("Failed to mark sales as processed. Check console for details.");
    }
  }

  async function handlePrintAndClear() {
    const salesData = displayedSales.filter(s => s.id); // Only process sales that have been saved to the DB
    if (!salesData.length) {
      alert("No sales records to print!");
      return;
    }
    const salesIds = salesData.map((s) => s.id);

    try {
      const res = await fetch("/sales/mark-printed", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": CSRF_TOKEN,
        },
        body: JSON.stringify({ sales_ids: salesIds }),
      });

      const text = await res.text();
      let data = {};
      try {
        data = JSON.parse(text);
      } catch {
        console.error("Invalid JSON from backend:", text);
        alert("Printing failed: Invalid server response.");
        return;
      }

      if (data.status !== "success") {
        alert(
          "Failed to process print request: " + (data.message || "Unknown error")
        );
        return;
      }

      const customerName = salesData[0].customer_code || "N/A";
      const billNo = data.bill_no || "";

      await printReceipt(
        buildFullReceiptHTML(salesData, billNo, customerName),
        customerName
      );

      const copyHtml =
        `<div style="text-align:center;font-size:2em;font-weight:bold;color:red;margin-bottom:10px;">COPY</div>` +
        buildFullReceiptHTML(salesData, billNo, customerName);
      await printReceipt(copyHtml, customerName + " - Copy");

      // Update the main sales state
      setAllSales((prevSales) =>
        prevSales.map((s) =>
          salesIds.includes(s.id)
            ? { ...s, bill_printed: 'Y', bill_no: billNo }
            : s
        )
      );

      setSelectedUnprintedCustomer(null);
      setSelectedPrintedCustomer(null);
    } catch (err) {
      console.error("Printing error:", err);
      alert("Printing failed. Check console for details.");
    }
  }

  // --- Print functions (unchanged) ---
  function buildFullReceiptHTML(salesData, billNo, customerName) {
    const date = new Date().toLocaleDateString();
    const time = new Date().toLocaleTimeString();
    let totalAmountSum = 0;
    let totalPacksSum = 0;
    const itemGroups = {};
    const givenAmount = salesData.reduce(
      (sum, s) => sum + (parseFloat(s.given_amount) || 0),
      0
    );
    const itemsHtml = salesData
      .map((s) => {
        totalAmountSum += parseFloat(s.total) || 0;
        const itemName = s.item_name || "";
        const weight = parseFloat(s.weight) || 0;
        const packs = parseInt(s.packs) || 0;
        totalPacksSum += packs;
        if (!itemGroups[itemName])
          itemGroups[itemName] = { totalWeight: 0, totalPacks: 0 };
        itemGroups[itemName].totalWeight += weight;
        itemGroups[itemName].totalPacks += packs;
        return `<tr style="font-size:1.2em;">
          <td style="text-align:left;">${itemName} <br>${packs}</td>
          <td style="text-align:right; padding-right:18px;">${weight.toFixed(
          2
        )}</td>
          <td style="text-align:right;">${(
            parseFloat(s.price_per_kg) || 0
          ).toFixed(2)}</td>
          <td style="text-align:right;">${(parseFloat(s.total) || 0).toFixed(
          2
        )}</td>
        </tr>`;
      })
      .join("");
    const packCostTotal = window.globalTotalPackCostValue || 0;
    const totalPrice = totalAmountSum;
    const remaining = givenAmount - (totalPrice + packCostTotal);
    const givenAmountRow =
      givenAmount > 0
        ? `<tr>
        <td style="width:50%; text-align:left;">දුන් මුදල: <strong>${givenAmount.toFixed(
          2
        )}</strong></td>
        <td style="width:50%; text-align:right;">ඉතිරිය: <strong>${Math.abs(
          remaining
        ).toFixed(2)}</strong></td>
      </tr>`
        : "";
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
        <tr><td>මුළු කුලිය:</td><td style="text-align:right;">${packCostTotal.toFixed(
      2
    )}</td></tr>
        <tr><td>මුළු අගය:</td><td style="text-align:right;">${(
        totalPrice + packCostTotal
      ).toFixed(2)}</td></tr>
        ${givenAmountRow}
      </table>
      <hr>
      <div style="text-align:center; font-size:10px;">
        <p>භාණ්ඩ පරීක්ෂාකර බලා රැගෙන යන්න</p>
        <p>නැවත භාර ගනු නොලැබේ</p>
      </div>
    </div>`;
  }

  async function printReceipt(html, customerName) {
    return new Promise((resolve) => {
      const printWindow = window.open("", "_blank");
      printWindow.document.write(`
        <!DOCTYPE html>
        <html>
          <head><title>${customerName}</title></head>
          <body>${html}</body>
        </html>
      `);
      printWindow.document.close();
      printWindow.focus();
      printWindow.print();
      setTimeout(() => {
        printWindow.close();
        resolve();
      }, 500);
    });
  }

  function handlePrintedCustomerClick(customerCode) {
    setSelectedPrintedCustomer(
      customerCode === selectedPrintedCustomer ? null : customerCode
    );
    setSelectedUnprintedCustomer(null);
  }

  function handleUnprintedCustomerClick(customerCode) {
    setSelectedUnprintedCustomer(
      customerCode === selectedUnprintedCustomer ? null : customerCode
    );
    setSelectedPrintedCustomer(null);
  }

  return (
    <div className="min-h-screen flex flex-row bg-gray-100 p-6">
      {/* Left section: Printed Customers */}
      <div className="w-1/4 bg-white shadow-xl rounded-xl p-4 mr-6 overflow-y-auto max-h-screen">
        <h2 className="text-xl font-bold mb-4">Printed Customers</h2>
        {/* --- New Search Bar for Printed Customers --- */}
        <div className="mb-4">
          <input
            type="text"
            placeholder="Search by Bill No or Code..."
            value={printedSearchQuery}
            onChange={(e) => setPrintedSearchQuery(e.target.value)}
            className="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300"
          />
        </div>
        {/* --- End of New Search Bar --- */}
        {printedCustomers.length === 0 ? (
          <p className="text-gray-500">No printed sales yet.</p>
        ) : (
          <div className="mb-6">
            <h3 className="font-semibold text-gray-700 mb-2">Customers</h3>
            <ul>
              {printedCustomers.map((customerCode) => (
                <li key={customerCode}>
                  <button
                    onClick={() => handlePrintedCustomerClick(customerCode)}
                    className={`w-full text-left p-3 mb-2 rounded-xl border ${selectedPrintedCustomer === customerCode
                      ? "bg-blue-500 text-white border-blue-600"
                      : "bg-gray-50 hover:bg-gray-100 border-gray-200"
                      }`}
                  >
                    <div className="font-medium">{customerCode}</div>
                    <div className="text-sm text-gray-600">
                      Sales:{" "}
                      {
                        printedSales.filter(
                          (s) => s.customer_code === customerCode
                        ).length
                      }
                    </div>
                  </button>
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      {/* Middle section: Form + Sales Table */}
      <div className="w-1/2 bg-white shadow-2xl rounded-3xl p-10">
        <h1 className="text-3xl font-bold text-center text-gray-800 mb-8">
          Sales Entry
        </h1>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="flex justify-between items-center bg-gray-50 p-4 rounded-xl shadow-sm">
            <span className="text-gray-600 font-medium">
              Bill No: {currentBillNo}
            </span>
            <h2 className="text-2xl font-bold text-red-600">
              Total Sales: Rs. {formatDecimal(mainTotal)}
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input
              ref={customerCodeRef}
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
              ref={customerSelectRef}
              value={form.customer_code}
              onChange={handleCustomerSelect}
              onKeyDown={(e) => handleKeyDown(e, 1)}
              className="px-4 py-2 border rounded-xl"
            >
              <option value="">-- Select Customer --</option>
              {customers.map((c) => (
                <option key={c.short_name} value={c.short_name}>
                  {c.name} ({c.short_name})
                </option>
              ))}
            </select>
            <select
              ref={grnSelectRef}
              value={form.grn_entry_code}
              onChange={handleGrnSelect}
              onKeyDown={(e) => handleKeyDown(e, 2)}
              className="px-4 py-2 border rounded-xl"
            >
              <option value="">-- Select GRN Entry --</option>
              {entries.map((en) => (
                <option key={en.code} value={en.code}>
                  {en.code} | {en.item_name}
                </option>
              ))}
            </select>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input
              ref={itemNameRef}
              type="text"
              value={form.item_name}
              readOnly
              placeholder="Item Name"
              onKeyDown={(e) => handleKeyDown(e, 3)}
              className="col-span-2 px-4 py-2 border bg-gray-100 rounded-xl"
            />
            <input
              ref={weightRef}
              name="weight"
              type="number"
              step="0.01"
              value={form.weight}
              onChange={(e) => handleInputChange(e, 4)}
              onKeyDown={(e) => handleKeyDown(e, 4)}
              placeholder="Weight (kg)"
              className="px-4 py-2 border rounded-xl"
            />
            <input
              ref={packsRef}
              name="packs"
              type="number"
              value={form.packs}
              onChange={(e) => handleInputChange(e, 5)}
              onKeyDown={(e) => handleKeyDown(e, 5)}
              placeholder="Packs"
              className="px-4 py-2 border rounded-xl"
            />
            <input
              ref={pricePerKgRef}
              name="price_per_kg"
              type="number"
              step="0.01"
              value={form.price_per_kg}
              onChange={(e) => handleInputChange(e, 6)}
              onKeyDown={(e) => handleKeyDown(e, 6)}
              placeholder="Price/kg"
              className="px-4 py-2 border rounded-xl"
            />
          </div>

          <input
            ref={totalRef}
            name="total"
            type="number"
            value={form.total}
            readOnly
            placeholder="Total"
            onKeyDown={(e) => handleKeyDown(e, 7)}
            className="w-full px-4 py-2 border bg-gray-100 rounded-xl"
          />

          <div className="flex space-x-4">
            <button
              type="submit"
              className="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition"
            >
              {editingSaleId ? "Update Sales Entry" : "Add Sales Entry"}
            </button>
            {editingSaleId && (
              <button
                type="button"
                onClick={handleDeleteClick}
                className="py-3 px-6 bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition"
              >
                Delete
              </button>
            )}
            <button
              type="button"
              onClick={handleClearForm}
              className="py-3 px-6 bg-gray-400 hover:bg-gray-500 text-white font-bold rounded-xl shadow-lg transition"
            >
              Clear
            </button>
          </div>
        </form>

        {errors.form && (
          <div className="mt-6 p-3 bg-red-100 text-red-700 rounded-xl">
            {errors.form}
          </div>
        )}

        <div className="mt-10">
          <h3 className="text-xl font-bold mb-4">
            {selectedPrintedCustomer
              ? `Sales for ${selectedPrintedCustomer}`
              : selectedUnprintedCustomer
                ? `Unprinted Sales for ${selectedUnprintedCustomer}`
                : "All New Sales"}
          </h3>
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
              <tbody>
                {displayedSales.map((s, idx) => (
                  <tr
                    key={s.id || idx}
                    className="text-center hover:bg-gray-50 cursor-pointer"
                    onClick={() => handleEditClick(s)}
                  >
                    <td className="px-4 py-2 border">{s.code}</td>
                    <td className="px-4 py-2 border">{s.customer_code}</td>
                    <td className="px-4 py-2 border">{s.item_name}</td>
                    <td className="px-4 py-2 border">
                      {formatDecimal(s.weight)}
                    </td>
                    <td className="px-4 py-2 border">
                      {formatDecimal(s.price_per_kg)}
                    </td>
                    <td className="px-4 py-2 border">{formatDecimal(s.total)}</td>
                    <td className="px-4 py-2 border">{s.packs}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {/* Right section: Unprinted Sales */}
      <div className="w-1/4 bg-white shadow-xl rounded-xl p-4 ml-6 overflow-y-auto max-h-screen">
        <h2 className="text-xl font-bold mb-4">Unprinted Sales</h2>
        <div className="bg-gray-50 p-3 rounded-xl shadow-sm mb-4">
          <h3 className="text-lg font-semibold text-gray-800">
            Total Unprinted:{" "}
            <span className="text-red-600 font-bold">
              Rs. {formatDecimal(unprintedTotal)}
            </span>
          </h3>
        </div>
        {/* --- New Search Bar --- */}
        <div className="mb-4">
          <input
            type="text"
            placeholder="Search by Customer Code..."
            value={unprintedSearchQuery}
            onChange={(e) => setUnprintedSearchQuery(e.target.value)}
            className="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300"
          />
        </div>
        {/* --- End of New Search Bar --- */}
        {unprintedCustomers.length === 0 ? (
          <p className="text-gray-500">No unprinted sales found.</p>
        ) : (
          <div className="mb-6">
            <h3 className="font-semibold text-gray-700 mb-2">Customers</h3>
            <ul>
              {unprintedCustomers.map((customerCode) => (
                <li key={customerCode}>
                  <button
                    onClick={() => handleUnprintedCustomerClick(customerCode)}
                    className={`w-full text-left p-3 mb-2 rounded-xl border ${selectedUnprintedCustomer === customerCode
                      ? "bg-blue-500 text-white border-blue-600"
                      : "bg-gray-50 hover:bg-gray-100 border-gray-200"
                      }`}
                  >
                    <div className="font-medium">{customerCode}</div>
                    <div className="text-sm text-gray-600">
                      Sales:{" "}
                      {
                        unprintedSales.filter(
                          (s) => s.customer_code === customerCode
                        ).length
                      }
                    </div>
                  </button>
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>
    </div>
  );
}