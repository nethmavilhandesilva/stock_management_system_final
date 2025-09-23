import React from 'react';
import { createRoot } from 'react-dom/client';
import DashboardApp from './components/DashboardApp';

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    const dashboardElement = document.getElementById('react-dashboard');
    if (dashboardElement) {
        const root = createRoot(dashboardElement);
        root.render(<DashboardApp />);
    }
});