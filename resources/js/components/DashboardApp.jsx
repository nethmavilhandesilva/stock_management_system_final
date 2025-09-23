import React, { useState, useEffect } from 'react';
import SalesEntryForm from './SalesEntryForm';
import SalesTable from './SalesTable';

const DashboardApp = () => {
    const [salesData, setSalesData] = useState([]);
    const [editingSale, setEditingSale] = useState(null);
    const [loading, setLoading] = useState(true);

    // Fetch sales data
    useEffect(() => {
        fetchSalesData();
    }, []);

    const fetchSalesData = async () => {
        try {
            const response = await fetch('/api/sales');
            const data = await response.json();
            setSalesData(data);
            setLoading(false);
        } catch (error) {
            console.error('Error fetching sales data:', error);
            setLoading(false);
        }
    };

    const handleSaleAdded = (newSale) => {
        setSalesData(prev => [...prev, newSale]);
    };

    const handleSaleUpdated = (updatedSale) => {
        setSalesData(prev => prev.map(sale => 
            sale.id === updatedSale.id ? updatedSale : sale
        ));
        setEditingSale(null);
    };

    const handleRowClick = (sale) => {
        setEditingSale(sale);
    };

    const handleCancelEdit = () => {
        setEditingSale(null);
    };

    if (loading) {
        return <div className="text-center p-4">Loading dashboard...</div>;
    }

    return (
        <div className="container-fluid" style={{ marginTop: '10px' }}>
            <div className="row justify-content-between">
                {/* Main Content Area */}
                <div className="col-custom-7">
                    <SalesEntryForm 
                        onSaleAdded={handleSaleAdded}
                        onSaleUpdated={handleSaleUpdated}
                        editingSale={editingSale}
                        onCancelEdit={handleCancelEdit}
                    />
                    
                    <SalesTable 
                        sales={salesData}
                        onRowClick={handleRowClick}
                    />
                </div>
            </div>
        </div>
    );
};

export default DashboardApp;