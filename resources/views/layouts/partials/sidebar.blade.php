<aside class="sidebar bg-white shadow-xl rounded-r-3xl flex flex-col justify-between h-screen sticky top-0">
    <div class="list-group shadow-sm rounded-3">
        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="material-icons me-2 text-primary">dashboard</span> Dashboard
        </a>
        <a href="{{ route('items.index') }}"
            class="list-group-item list-group-item-action d-flex align-items-center active">
            <span class="material-icons me-2 text-success">inventory_2</span> භාණ්ඩ (Items)
        </a>

        <a href="{{ route('customers.index') }}"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-blue-100 text-gray-700 transition">
            <span class="material-icons text-primary mr-2">people</span> Customers
        </a>
        <a href="{{ route('suppliers.index') }}"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 transition">
            <span class="material-icons text-blue-600 mr-3">local_shipping</span> සැපයුම්කරුවන් (Suppliers)
        </a>
        <a href="{{ route('grn.index') }}"
            class="flex items-center px-3 py-2 rounded-lg hover:bg-blue-50 text-gray-700 transition">
            <span class="material-icons text-blue-600 mr-3">assignment_turned_in</span> GRN-4
        </a>
       


    </div>
</aside>