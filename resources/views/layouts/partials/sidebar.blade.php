<aside class="sidebar bg-white shadow-xl rounded-r-3xl flex flex-col justify-between h-screen sticky top-0">
    <div class="list-group shadow-sm rounded-3">
        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="material-icons me-2 text-primary">dashboard</span>උපකරණ පුවරුව(Dashboard)
        </a>
        <a href="{{ route('items.index') }}"
            class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="material-icons me-2 text-success">inventory_2</span> භාණ්ඩ (Items)
        </a>

        <a href="{{ route('customers.index') }}"
           class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="material-icons text-primary mr-2">people</span>පාරිභෝගිකයින්(Customers)
        </a>
        <a href="{{ route('suppliers.index') }}"
            class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="material-icons text-blue-600 mr-3">local_shipping</span> සැපයුම්කරුවන් (Suppliers)
        </a>
        <a href="{{ route('grn.index') }}"
            class="list-group-item list-group-item-action d-flex align-items-center">
            <span class="material-icons text-blue-600 mr-3">assignment_turned_in</span> GRN
        </a>
       


    </div>
</aside>