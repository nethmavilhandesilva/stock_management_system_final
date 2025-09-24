import React, { useState, useEffect } from "react";
import { useMemo } from "react";

export default function SalesEntry() {
  const initialSales = window.__INITIAL_SALES__ || [];
  const customers = window.__CUSTOMERS__ || [];
  const entries = window.__ENTRIES__ || [];
  const initialPrintedSales = window.__PRINTED_SALES__ || []; // Get from backend
  const initialUnprintedSales = window.__UNPRINTED_SALES__ || []; // New data from backend
  const STORE_URL = window.__STORE_URL__ || "/grn";
  const CSRF_TOKEN =
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

  // --- State ---
  const [sales, setSales] = useState(initialSales);
  const [printedSales, setPrintedSales] = useState(initialPrintedSales);
  const [unprintedSales, setUnprintedSales] = useState(initialUnprintedSales);
  const [selectedPrintedCustomer, setSelectedPrintedCustomer] = useState(null);
  const [selectedUnprintedCustomer, setSelectedUnprintedCustomer] = useState(null);
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

  // --- Filtered Data & Display Logic ---
  const printedCustomers = [...new Set(printedSales.map(s => s.customer_code))];
  const unprintedCustomers = [...new Set(unprintedSales.map(s => s.customer_code))];

  // Compute displayed sales dynamically
  const displayedSales = useMemo(() => {
  if (selectedPrintedCustomer) {
    return [...printedSales, ...sales].filter(
      s => s.customer_code === selectedPrintedCustomer
    );
  }

  if (selectedUnprintedCustomer) {
    return [...unprintedSales, ...sales].filter(
      s => s.customer_code === selectedUnprintedCustomer
    );
  }

  // Only show new entries in the main table; unprinted panel stays intact
  return [...sales];
}, [printedSales, unprintedSales, sales, selectedPrintedCustomer, selectedUnprintedCustomer]);

  // --- Form handlers ---
  function handleInputChange(e) {
    const { name, value } = e.target;
    setForm(prev => ({ ...prev, [name]: value }));
  }

  function handleCustomerSelect(e) {
    const short = e.target.value;
    const c = customers.find(x => String(x.short_name) === String(short));
    setForm(prev => ({
      ...prev,
      customer_code: short || prev.customer_code,
      customer_name: c?.name || "",
    }));
  }

  function handleGrnSelect(e) {
    const code = e.target.value;
    const entry = entries.find(x => String(x.code) === String(code));
    if (entry) {
      setForm(prev => ({
        ...prev,
        grn_entry_code: code,
        supplier_code: entry.supplier_code || prev.supplier_code || "",
        code: entry.code || prev.code || "",
        item_code: entry.item_code || entry.itemCode || prev.item_code || "",
        item_name: entry.item_name || entry.itemName || prev.item_name || "",
        price_per_kg: entry.price_per_kg ?? entry.PerKGPrice ?? entry.SalesKGPrice ?? prev.price_per_kg ?? "",
        original_weight: entry.original_weight ?? prev.original_weight ?? "",
        original_packs: entry.original_packs ?? prev.original_packs ?? "",
      }));
      setGrnPriceDisplay(entry.price_per_kg ?? entry.PerKGPrice ?? entry.SalesKGPrice ?? "");
    } else {
      setForm(prev => ({
        ...prev,
        grn_entry_code: "",
        code: "",
        item_code: "",
        item_name: "",
        price_per_kg: "",
        original_weight: "",
        original_packs: "",
      }));
      setGrnPriceDisplay("");
    }
  }

  // --- Calculate total ---
  useEffect(() => {
    const w = parseFloat(form.weight) || 0;
    const p = parseFloat(form.price_per_kg) || 0;
    const tot = w * p;
    setForm(prev => ({
      ...prev,
      total: tot ? Number(tot.toFixed(2)) : "",
    }));
  }, [form.weight, form.price_per_kg]);

  // --- Submit handler ---
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
      const res = await fetch(STORE_URL, {
        method: "POST",
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

      const newSale = data.data || {};
      setSales(prev => [...prev, newSale]);

      setForm({
        customer_code: form.customer_code,
        customer_name: form.customer_name,
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
      setGrnPriceDisplay("");
      window.currentDisplayedSalesData = [...sales, newSale];
    } catch (err) {
      setErrors({ form: err.message || "Network or server error" });
    }
  }

  function formatDecimal(val) {
    const n = parseFloat(val);
    return Number.isFinite(n) ? n.toFixed(2) : "0.00";
  }

  const mainTotal = displayedSales.reduce((acc, s) => {
    const t = parseFloat(s.total) || parseFloat(s.weight || 0) * parseFloat(s.price_per_kg || 0);
    return acc + (isNaN(t) ? 0 : t);
  }, 0);

  const unprintedTotal = unprintedSales.reduce((acc, s) => {
    const t = parseFloat(s.total) || parseFloat(s.weight || 0) * parseFloat(s.price_per_kg || 0);
    return acc + (isNaN(t) ? 0 : t);
  }, 0);

  // --- F1: Print & Clear ---
  useEffect(() => {
    function handleKeyDown(e) {
      if (e.key === "F1") {
        e.preventDefault();
        handlePrintAndClear();
      }
    }
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [sales]);

  // --- F5: Mark all processed ---
  useEffect(() => {
    function handleKeyDown(e) {
      if (e.key === "F5") {
        e.preventDefault();
        markAllSalesAsProcessed();
      }
    }
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [sales]); // <-- The key change: add 'sales' as a dependency

  async function markAllSalesAsProcessed() {
  const salesToProcess = sales.map(s => s.id);

  if (!salesToProcess.length) {
    alert("No sales to process.");
    return;
  }

  if (!window.confirm("Are you sure you want to mark ALL sales as processed?")) return;

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

      // Update unprinted sales locally (keep middle section intact)
      const updatedSales = sales.map(s => ({
        ...s,
        bill_printed: "N",
      }));

      // Only update unprintedSales if you want to track newly processed sales
      setUnprintedSales(prev => [...prev, ...updatedSales.filter(s => s.bill_printed === "N")]);

      // Clear the main table only
      setSales([]);

      // Do NOT clear selectedPrintedCustomer or selectedUnprintedCustomer
      // So the left/middle panels remain visible
      window.__UNPRINTED_SALES__ = [...unprintedSales];
      window.currentDisplayedSalesData = [];
    } else {
      alert(data.message || "Failed to mark sales as processed.");
    }
  } catch (err) {
    console.error("Error marking sales as processed:", err);
    alert("Failed to mark sales as processed. Check console for details.");
  }
}



  async function handlePrintAndClear() {
    const salesData = window.currentDisplayedSalesData || [];
    if (!salesData.length) {
      alert("No sales records to print!");
      return;
    }

    const salesIds = salesData.map((s) => s.id);

    try {
      // 1️⃣ Mark as printed & get bill_no
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
        alert("Failed to process print request: " + (data.message || "Unknown error"));
        return;
      }

      const customerName = salesData[0].customer_code || "N/A";
      const billNo = data.bill_no || "";

      // Print main receipt
      await printReceipt(buildFullReceiptHTML(salesData, billNo, customerName), customerName);

      // Optional: Print COPY
      const copyHtml = `<div style="text-align:center;font-size:2em;font-weight:bold;color:red;margin-bottom:10px;">COPY</div>` + buildFullReceiptHTML(salesData, billNo, customerName);
      await printReceipt(copyHtml, customerName + " - Copy");

      // Clear table and update printed sales
      setSales(prev => prev.filter(s => !salesIds.includes(s.id)));
      setUnprintedSales(prev => [...prev, ...salesData.map(s => ({ ...s, bill_printed: 'N' }))]); // Update unprinted list
      setPrintedSales(prev => [...prev, ...salesData.map(s => ({ ...s, bill_printed: 'Y' }))]);
      window.currentDisplayedSalesData = [];
    } catch (err) {
      console.error("Printing error:", err);
      alert("Printing failed. Check console for details.");
    }
  }

  function buildFullReceiptHTML(salesData, billNo, customerName) {
    const date = new Date().toLocaleDateString();
    const time = new Date().toLocaleTimeString();

    let totalAmountSum = 0;
    let totalPacksSum = 0;
    const itemGroups = {};

    const givenAmount = salesData.reduce((sum, s) => sum + (parseFloat(s.given_amount) || 0), 0);

    const itemsHtml = salesData
      .map((s) => {
        totalAmountSum += parseFloat(s.total) || 0;
        const itemName = s.item_name || "";
        const weight = parseFloat(s.weight) || 0;
        const packs = parseInt(s.packs) || 0;
        totalPacksSum += packs;
        if (!itemGroups[itemName]) itemGroups[itemName] = { totalWeight: 0, totalPacks: 0 };
        itemGroups[itemName].totalWeight += weight;
        itemGroups[itemName].totalPacks += packs;

        return `<tr style="font-size:1.2em;">
          <td style="text-align:left;">${itemName} <br>${packs}</td>
          <td style="text-align:right; padding-right:18px;">${weight.toFixed(2)}</td>
          <td style="text-align:right;">${(parseFloat(s.price_per_kg) || 0).toFixed(2)}</td>
          <td style="text-align:right;">${(parseFloat(s.total) || 0).toFixed(2)}</td>
        </tr>`;
      })
      .join("");

    const packCostTotal = window.globalTotalPackCostValue || 0;
    const totalPrice = totalAmountSum;
    const remaining = givenAmount - (totalPrice + packCostTotal);

    const givenAmountRow =
      givenAmount > 0
        ? `<tr>
        <td style="width:50%; text-align:left;">දුන් මුදල: <strong>${givenAmount.toFixed(2)}</strong></td>
        <td style="width:50%; text-align:right;">ඉතිරිය: <strong>${Math.abs(remaining).toFixed(2)}</strong></td>
      </tr>`
        : "";

    // Final receipt HTML
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
    setSelectedPrintedCustomer(customerCode === selectedPrintedCustomer ? null : customerCode);
    setSelectedUnprintedCustomer(null);
  }

  function handleUnprintedCustomerClick(customerCode) {
    setSelectedUnprintedCustomer(customerCode === selectedUnprintedCustomer ? null : customerCode);
    setSelectedPrintedCustomer(null);
  }

  return (
    <div className="min-h-screen flex flex-row bg-gray-100 p-6">
      {/* Left section: Printed Customers */}
      <div className="w-1/4 bg-white shadow-xl rounded-xl p-4 mr-6 overflow-y-auto max-h-screen">
        <h2 className="text-xl font-bold mb-4">Printed Customers</h2>
        {printedSales.length === 0 ? (
          <p className="text-gray-500">No printed sales yet.</p>
        ) : (
          <div className="mb-6">
            <h3 className="font-semibold text-gray-700 mb-2">Customers</h3>
            <ul>
              {printedCustomers.map(customerCode => (
                <li key={customerCode}>
                  <button
                    onClick={() => handlePrintedCustomerClick(customerCode)}
                    className={`w-full text-left p-3 mb-2 rounded-xl border ${selectedPrintedCustomer === customerCode
                        ? 'bg-blue-500 text-white border-blue-600'
                        : 'bg-gray-50 hover:bg-gray-100 border-gray-200'
                      }`}
                  >
                    <div className="font-medium">
                      {customerCode}
                    </div>
                    <div className="text-sm text-gray-600">
                      Sales: {printedSales.filter(s => s.customer_code === customerCode).length}
                    </div>
                  </button>
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      {/* Middle section: Unprinted Sales */}
      <div className="w-1/4 bg-white shadow-xl rounded-xl p-4 mr-6 overflow-y-auto max-h-screen">
        <h2 className="text-xl font-bold mb-4">Unprinted Sales</h2>
        <div className="bg-gray-50 p-3 rounded-xl shadow-sm mb-4">
          <h3 className="text-lg font-semibold text-gray-800">Total Unprinted: <span className="text-red-600 font-bold">Rs. {formatDecimal(unprintedTotal)}</span></h3>
        </div>
        {unprintedSales.length === 0 ? (
          <p className="text-gray-500">No unprinted sales.</p>
        ) : (
          <div className="mb-6">
            <h3 className="font-semibold text-gray-700 mb-2">Customers</h3>
            <ul>
              {unprintedCustomers.map(customerCode => (
                <li key={customerCode}>
                  <button
                    onClick={() => handleUnprintedCustomerClick(customerCode)}
                    className={`w-full text-left p-3 mb-2 rounded-xl border ${selectedUnprintedCustomer === customerCode
                        ? 'bg-blue-500 text-white border-blue-600'
                        : 'bg-gray-50 hover:bg-gray-100 border-gray-200'
                      }`}
                  >
                    <div className="font-medium">
                      {customerCode}
                    </div>
                    <div className="text-sm text-gray-600">
                      Sales: {unprintedSales.filter(s => s.customer_code === customerCode).length}
                    </div>
                  </button>
                </li>
              ))}
            </ul>
          </div>
        )}
      </div>

      {/* Right section: Form + Sales Table */}
      <div className="w-1/2 bg-white shadow-2xl rounded-3xl p-10">
        <h1 className="text-3xl font-bold text-center text-gray-800 mb-8">Sales Entry</h1>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="flex justify-between items-center bg-gray-50 p-4 rounded-xl shadow-sm">
            <span className="text-gray-600 font-medium">Bill No:</span>
            <h2 className="text-2xl font-bold text-red-600">Total Sales: Rs. {formatDecimal(mainTotal)}</h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input
              name="customer_code"
              value={form.customer_code}
              onChange={handleInputChange}
              type="text"
              maxLength={10}
              placeholder="Customer Code"
              className="px-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-300"
            />
            <select value={form.customer_code} onChange={handleCustomerSelect} className="px-4 py-2 border rounded-xl">
              <option value="">-- Select Customer --</option>
              {customers.map(c => (
                <option key={c.short_name} value={c.short_name}>
                  {c.name} ({c.short_name})
                </option>
              ))}
            </select>
            <select value={form.grn_entry_code} onChange={handleGrnSelect} className="px-4 py-2 border rounded-xl">
              <option value="">-- Select GRN Entry --</option>
              {entries.map(en => (
                <option key={en.code} value={en.code}>
                  {en.code} | {en.item_name}
                </option>
              ))}
            </select>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" value={form.item_name} readOnly placeholder="Item Name" className="col-span-2 px-4 py-2 border bg-gray-100 rounded-xl" />
            <input name="weight" type="number" step="0.01" value={form.weight} onChange={handleInputChange} placeholder="Weight (kg)" className="px-4 py-2 border rounded-xl" />
            <input name="price_per_kg" type="number" step="0.01" value={form.price_per_kg} onChange={handleInputChange} placeholder="Price/kg" className="px-4 py-2 border rounded-xl" />
            <input name="packs" type="number" value={form.packs} onChange={handleInputChange} placeholder="Packs" className="px-4 py-2 border rounded-xl" />
          </div>

          <input name="total" type="number" value={form.total} readOnly placeholder="Total" className="w-full px-4 py-2 border bg-gray-100 rounded-xl" />

          <button type="submit" className="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg transition">
            Add Sales Entry
          </button>
        </form>

        {errors.form && <div className="mt-6 p-3 bg-red-100 text-red-700 rounded-xl">{errors.form}</div>}

        {/* Main Sales Table (Dynamically displays Unprinted or Printed sales) */}
        <div className="mt-10">
          <h3 className="text-xl font-bold mb-4">
            {selectedPrintedCustomer
              ? `Sales for ${selectedPrintedCustomer}`
              : selectedUnprintedCustomer
                ? `Unprinted Sales for ${selectedUnprintedCustomer}`
                : "All Unprinted Sales"}
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
                  <tr key={idx} className="text-center hover:bg-gray-50">
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
    </div>
  );
}