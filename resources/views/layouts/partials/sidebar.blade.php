<aside class="sidebar shadow-xl rounded-r-3xl flex flex-col justify-between h-screen sticky top-0"
    style="background-color: #006400 !important;"> {{-- Background color applied here --}}
    <div class="list-group shadow-sm rounded-3">
        <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons me-2 text-primary">dashboard</span><span class="text-white"> උපකරණ
                පුවරුව(Dashboard)</span>
        </a>
        <a href="{{ route('items.index') }}" class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons me-2 text-success">inventory_2</span> <span class="text-white">භාණ්ඩ
                (Items)</span>
        </a>

        <a href="{{ route('customers.index') }}"
            class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons text-primary mr-2">people</span> <span
                class="text-white">පාරිභෝගිකයින්(Customers)</span>
        </a>
        <a href="{{ route('suppliers.index') }}"
            class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons text-blue-600 mr-3">local_shipping</span> <span class="text-white">සැපයුම්කරුවන්
                (Suppliers)</span>
        </a>
        <a href="{{ route('grn.index') }}" class="list-group-item list-group-item-action d-flex align-items-center"
            style="background-color: transparent !important; color: white !important;"> {{-- Text color to white --}}
            <span class="material-icons text-blue-600 mr-3">assignment_turned_in</span> <span
                class="text-white">GRN</span>
        </a>

        <!-- Reports Dropdown -->
        <div class="dropdown">
            <a class="list-group-item list-group-item-action d-flex align-items-center dropdown-toggle" href="#"
                id="reportsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"
                style="background-color: transparent !important; color: white !important;">
                <span class="material-icons text-white me-2">assessment</span>
                <span class="text-white">වාර්තා (Reports)</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-dark bg-success border-0 rounded-3 mt-1"
                aria-labelledby="reportsDropdown">
                <li>
                    <a class="dropdown-item text-white" href="#" data-bs-toggle="modal"
                        data-bs-target="#reportFilterModal">
                        සැපයුම්කරු
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-white" href="#" data-bs-toggle="modal"
                        data-bs-target="#itemReportModal">
                        එළවළු
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-white" href="#" data-bs-toggle="modal" data-bs-target="#weight_modal">
                        බර මත
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-white" href="#" data-bs-toggle="modal"
                        data-bs-target="#grnSaleReportModal">
                        මිල එක්කතුව
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-white" href="#" data-bs-toggle="modal"
                        data-bs-target="#reportFilterModal1">
                        විකුණුම්
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-white d-flex align-items-center"
                        href="{{ route('report.grn.sales.overview') }}" target="_blank"> {{-- Direct link to the report
                        route, opens in new tab --}}
                        <span class="material-icons me-2" style="font-size: 18px;">storage</span> {{-- Or an icon that
                        fits --}}
                        ඉතිරි වාර්තාව 1
                    </a>
                </li>
                <li>
                    <a class="dropdown-item text-white d-flex align-items-center"
                        href="{{ route('report.grn.sales.overview2') }}" target="_blank"> {{-- Direct link to the report
                        route, opens in new tab --}}
                        <span class="material-icons me-2" style="font-size: 18px;">storage</span> {{-- Or an icon that
                        fits --}}
                        ඉතිරි වාර්තාව 2
                    </a>
                </li>
            </ul>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</aside>