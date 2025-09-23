import React, { useState, useEffect, useRef } from 'react';

const SalesEntryForm = ({ onSaleAdded, onSaleUpdated, editingSale, onCancelEdit }) => {
    // Form state
    const [formData, setFormData] = useState({
        customer_code: '',
        customer_name: '',
        grn_entry_code: '',
        supplier_code: '',
        item_code: '',
        item_name: '',
        weight: '',
        price_per_kg: '',
        packs: '',
        total: '0.00',
        given_amount: ''
    });

    // UI state
    const [remainingStock, setRemainingStock] = useState({ 
        weight: 0, 
        packs: 0 
    });
    const [grnData, setGrnData] = useState(null);
    const [loanAmount, setLoanAmount] = useState(0);
    const [customers, setCustomers] = useState([]);
    const [grnEntries, setGrnEntries] = useState([]);
    const [loading, setLoading] = useState(false);

    const customerCodeRef = useRef(null);
    const weightFieldRef = useRef(null);

    // Initialize form when component mounts
    useEffect(() => {
        fetchInitialData();
        if (customerCodeRef.current) {
            customerCodeRef.current.focus();
        }
    }, []);

    // Populate form when editing a sale
    useEffect(() => {
        if (editingSale) {
            setFormData({
                customer_code: editingSale.customer_code || '',
                customer_name: editingSale.customer_name || '',
                grn_entry_code: editingSale.code || '',
                supplier_code: editingSale.supplier_code || '',
                item_code: editingSale.item_code || '',
                item_name: editingSale.item_name || '',
                weight: editingSale.weight ? parseFloat(editingSale.weight).toFixed(2) : '',
                price_per_kg: editingSale.price_per_kg ? parseFloat(editingSale.price_per_kg).toFixed(2) : '',
                packs: editingSale.packs || '',
                total: editingSale.total ? parseFloat(editingSale.total).toFixed(2) : '0.00',
                given_amount: editingSale.given_amount || ''
            });

            // Fetch GRN data for the editing sale
            if (editingSale.code) {
                fetchGRNData(editingSale.code);
            }
        }
    }, [editingSale]);

    // Calculate total when weight or price changes
    useEffect(() => {
        const weight = parseFloat(formData.weight) || 0;
        const price = parseFloat(formData.price_per_kg) || 0;
        const total = (weight * price).toFixed(2);
        
        if (total !== formData.total) {
            setFormData(prev => ({ ...prev, total }));
        }
    }, [formData.weight, formData.price_per_kg]);

    // Fetch initial data (customers, GRN entries, etc.)
    const fetchInitialData = async () => {
        try {
            const response = await fetch('/api/dashboard/initial-data');
            const data = await response.json();
            setCustomers(data.customers || []);
            setGrnEntries(data.grnEntries || []);
        } catch (error) {
            console.error('Error fetching initial data:', error);
        }
    };

    // Fetch GRN data when GRN entry is selected
    const fetchGRNData = async (grnCode) => {
        if (!grnCode) return;
        
        try {
            const response = await fetch(`/api/grn-entry/${grnCode}`);
            const data = await response.json();
            
            if (data) {
                setGrnData(data);
                setRemainingStock({
                    weight: parseFloat(data.weight) || 0,
                    packs: parseInt(data.packs) || 0
                });

                // Auto-fill price if available
                if (data.SalesKGPrice && !formData.price_per_kg) {
                    setFormData(prev => ({ 
                        ...prev, 
                        price_per_kg: data.SalesKGPrice 
                    }));
                }
            }
        } catch (error) {
            console.error('Error fetching GRN data:', error);
        }
    };

    // Fetch loan amount when customer changes
    const fetchLoanAmount = async (customerCode) => {
        if (!customerCode) {
            setLoanAmount(0);
            return;
        }

        try {
            const response = await fetch('/api/customer/loan-amount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ customer_short_name: customerCode })
            });
            
            const data = await response.json();
            setLoanAmount(parseFloat(data.total_loan_amount) || 0);
        } catch (error) {
            console.error('Error fetching loan amount:', error);
            setLoanAmount(0);
        }
    };

    // Handle form input changes
    const handleInputChange = (field, value) => {
        setFormData(prev => ({ ...prev, [field]: value }));

        // Special handling for customer code
        if (field === 'customer_code') {
            fetchLoanAmount(value);
            
            // Find customer name
            const customer = customers.find(c => c.short_name === value);
            if (customer) {
                setFormData(prev => ({ ...prev, customer_name: customer.name }));
            }
        }

        // Special handling for GRN entry
        if (field === 'grn_entry_code') {
            fetchGRNData(value);
            
            // Find GRN entry details
            const grnEntry = grnEntries.find(entry => entry.code === value);
            if (grnEntry) {
                setFormData(prev => ({
                    ...prev,
                    supplier_code: grnEntry.supplier_code,
                    item_code: grnEntry.item_code,
                    item_name: grnEntry.item_name
                }));
            }
        }
    };

    // Handle form submission
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);

        try {
            const url = editingSale ? 
                `/sales/update/${editingSale.id}` : 
                '/grn/store';
                
            const method = editingSale ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                if (editingSale) {
                    onSaleUpdated(result.data);
                } else {
                    onSaleAdded(result.data);
                    resetForm();
                }
                
                // Show success message
                showAlert('Success! Sale entry saved successfully.', 'success');
            } else {
                showAlert('Error: ' + result.message, 'error');
            }
        } catch (error) {
            console.error('Error saving sale:', error);
            showAlert('Network error occurred. Please try again.', 'error');
        } finally {
            setLoading(false);
        }
    };

    // Reset form to initial state
    const resetForm = () => {
        setFormData({
            customer_code: '',
            customer_name: '',
            grn_entry_code: '',
            supplier_code: '',
            item_code: '',
            item_name: '',
            weight: '',
            price_per_kg: '',
            packs: '',
            total: '0.00',
            given_amount: ''
        });
        setRemainingStock({ weight: 0, packs: 0 });
        setGrnData(null);
        setLoanAmount(0);
        
        if (customerCodeRef.current) {
            customerCodeRef.current.focus();
        }
    };

    // Handle keyboard navigation
    const handleKeyDown = (e, nextField) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (nextField && nextField.current) {
                nextField.current.focus();
                nextField.current.select();
            }
        }
    };

    // Show alert messages
    const showAlert = (message, type) => {
        // You can replace this with a proper alert component
        alert(message);
    };

    return (
        <div className="card shadow-sm border-0 rounded-3 p-2">
            {/* Total Sales Display */}
            <div style={{ display: 'flex', alignItems: 'center', gap: '6px', marginBottom: '10px' }}>
                <h5 style={{ fontSize: '1.5rem', color: 'red', margin: 0, whiteSpace: 'nowrap' }}>
                    <strong>Total Sales Value:</strong> Rs. 
                    <span id="mainTotalSalesValue">0.00</span>
                </h5>
            </div>

            <form onSubmit={handleSubmit} id="salesEntryForm">
                {/* Customer Selection Row */}
                <div className="row justify-content-end">
                    <div className="row g-2 align-items-center">
                        {/* Customer Code Input */}
                        <div className="col-md-3">
                            <input
                                type="text"
                                ref={customerCodeRef}
                                value={formData.customer_code}
                                onChange={(e) => handleInputChange('customer_code', e.target.value.toUpperCase())}
                                maxLength="10"
                                className="form-control text-uppercase"
                                placeholder="පාරිභෝගික කේතය"
                                style={{ 
                                    width: '140px', 
                                    height: '34px', 
                                    fontSize: '14px', 
                                    padding: '6px 12px', 
                                    border: '1px solid black', 
                                    color: 'black' 
                                }}
                                required
                            />
                        </div>

                        {/* Customer Select Dropdown */}
                        <div className="col-md-6">
                            <select
                                value={formData.customer_code}
                                onChange={(e) => handleInputChange('customer_code', e.target.value)}
                                className="form-select form-select-sm"
                                style={{ 
                                    width: '160px', 
                                    height: '34px', 
                                    fontSize: '14px', 
                                    padding: '6px 12px' 
                                }}
                            >
                                <option value="">-- පාරිභෝගිකයා තෝරන්න --</option>
                                {customers.map(customer => (
                                    <option key={customer.id} value={customer.short_name}>
                                        {customer.name} ({customer.short_name})
                                    </option>
                                ))}
                            </select>
                        </div>

                        {/* Loan Amount Display */}
                        <div className="col-md-3">
                            <div 
                                className="form-control"
                                style={{ 
                                    width: '80px', 
                                    height: '34px', 
                                    fontSize: '14px', 
                                    padding: '6px 12px', 
                                    border: '1px solid black', 
                                    color: 'black', 
                                    backgroundColor: '#f0f0f0', 
                                    textAlign: 'right' 
                                }}
                            >
                                {loanAmount.toFixed(2)}
                            </div>
                        </div>
                    </div>
                </div>

                {/* GRN Selection */}
                <div className="row mt-2">
                    <div className="col-md-12">
                        <select
                            value={formData.grn_entry_code}
                            onChange={(e) => handleInputChange('grn_entry_code', e.target.value)}
                            className="form-select"
                            style={{
                                height: '45px',
                                fontSize: '16px',
                                padding: '8px 16px',
                                border: '1px solid black',
                                color: 'black',
                                textTransform: 'uppercase'
                            }}
                        >
                            <option value="">-- Select GRN Entry --</option>
                            {grnEntries.map(entry => (
                                <option key={entry.id} value={entry.code}>
                                    {entry.code} | {entry.supplier_code} | {entry.item_code} | 
                                    {entry.item_name} | {entry.packs} | {entry.grn_no} | 
                                    {entry.txn_date}
                                </option>
                            ))}
                        </select>
                        
                        {/* GRN Price Display */}
                        {grnData && grnData.SalesKGPrice && (
                            <small style={{ color: 'red', fontSize: '14px', display: 'block', marginTop: '4px' }}>
                                GRN Price: {grnData.SalesKGPrice}
                            </small>
                        )}
                    </div>
                </div>

                {/* Item Details Section */}
                <div className="d-flex flex-wrap gap-2 align-items-start mt-2">
                    {/* Item Name Display */}
                    <div style={{ flex: '1.5 1 150px' }}>
                        <input
                            type="text"
                            value={formData.item_name}
                            readOnly
                            className="form-control"
                            placeholder="අයිතමයේ නම (Item Name)"
                            style={{
                                backgroundColor: '#e9ecef',
                                color: 'black',
                                height: '45px',
                                fontSize: '18px',
                                padding: '6px 10px',
                                border: '1px solid black'
                            }}
                        />
                    </div>

                    {/* Weight Input */}
                    <div style={{ width: '100px' }}>
                        <input
                            ref={weightFieldRef}
                            type="number"
                            name="weight"
                            step="0.01"
                            value={formData.weight}
                            onChange={(e) => handleInputChange('weight', e.target.value)}
                            onKeyDown={(e) => handleKeyDown(e, pricePerKgRef)}
                            className="form-control"
                            placeholder="බර (kg)"
                            required
                            style={{
                                height: '45px',
                                fontSize: '18px',
                                padding: '6px 10px',
                                border: '1px solid black',
                                color: 'black'
                            }}
                        />
                        <small className="form-text text-danger fw-bold" style={{ fontSize: '1.1rem' }}>
                            BW: {remainingStock.weight.toFixed(2)}
                        </small>
                    </div>

                    {/* Price per KG */}
                    <div style={{ flex: '1 1 80px', position: 'relative' }}>
                        <input
                            type="number"
                            name="price_per_kg"
                            step="0.01"
                            value={formData.price_per_kg}
                            onChange={(e) => handleInputChange('price_per_kg', e.target.value)}
                            onKeyDown={(e) => handleKeyDown(e, packsRef)}
                            className="form-control"
                            placeholder="මිල (Price/kg)"
                            required
                            style={{
                                height: '45px',
                                fontSize: '18px',
                                padding: '6px 10px',
                                border: '1px solid black',
                                color: 'black'
                            }}
                        />
                    </div>

                    {/* Packs Input */}
                    <div style={{ flex: '1 1 80px' }}>
                        <input
                            type="number"
                            name="packs"
                            value={formData.packs}
                            onChange={(e) => handleInputChange('packs', e.target.value)}
                            className="form-control"
                            placeholder="ඇසුරුම් (Packs)"
                            required
                            style={{
                                height: '45px',
                                fontSize: '18px',
                                padding: '6px 10px',
                                border: '1px solid black',
                                color: 'black'
                            }}
                        />
                        <small className="form-text text-danger fw-bold" style={{ fontSize: '1.3rem' }}>
                            BP: {remainingStock.packs}
                        </small>
                    </div>

                    {/* Total Display */}
                    <div style={{ flex: '1.5 1 120px' }}>
                        <input
                            type="number"
                            name="total"
                            value={formData.total}
                            readOnly
                            className="form-control bg-light"
                            placeholder="සමස්ත (Total)"
                            style={{
                                height: '45px',
                                fontSize: '18px',
                                padding: '6px 10px',
                                border: '1px solid black'
                            }}
                        />
                    </div>
                </div>

                {/* Hidden Fields */}
                <input type="hidden" name="customer_name" value={formData.customer_name} />
                <input type="hidden" name="supplier_code" value={formData.supplier_code} />
                <input type="hidden" name="item_code" value={formData.item_code} />

                {/* Action Buttons */}
                <div className="d-grid gap-2 d-md-flex justify-content-center mt-4">
                    <button 
                        type="submit" 
                        className="btn btn-primary btn-sm shadow-sm"
                        disabled={loading}
                    >
                        <i className="material-icons me-2">add_circle_outline</i>
                        {loading ? 'Processing...' : (editingSale ? 'Update Sales Entry' : 'Add Sales Entry')}
                    </button>
                    
                    {editingSale && (
                        <>
                            <button 
                                type="button" 
                                className="btn btn-danger btn-sm shadow-sm"
                                onClick={onCancelEdit}
                            >
                                <i className="material-icons me-2">delete</i>Delete Sales Entry
                            </button>
                            <button 
                                type="button" 
                                className="btn btn-secondary btn-sm shadow-sm"
                                onClick={onCancelEdit}
                            >
                                <i className="material-icons me-2">cancel</i>Cancel / New Entry
                            </button>
                        </>
                    )}
                </div>
            </form>

            {/* Given Amount Input */}
            <div className="mt-3 d-flex justify-content-end">
                <div className="w-25">
                    <input
                        type="number"
                        step="0.01"
                        name="given_amount"
                        value={formData.given_amount}
                        onChange={(e) => handleInputChange('given_amount', e.target.value)}
                        className="form-control form-control-sm text-end"
                        placeholder="දුන් මුදල"
                    />
                </div>
            </div>
        </div>
    );
};

// Create refs for keyboard navigation
const pricePerKgRef = React.createRef();
const packsRef = React.createRef();

export default SalesEntryForm;