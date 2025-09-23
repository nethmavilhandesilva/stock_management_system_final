<!doctype html>
<html>
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sales Entry</title>
  @viteReactRefresh
  @vite(['resources/js/app.jsx'])
</head>
<body>
  <div class="container">
    <!-- React will completely render the form + table into this DIV -->
    <div id="salesApp"></div>
  </div>

  <script>
    // pass server data to React via window.* variables
    window.__INITIAL_SALES__ = @json($sales->toArray());
    window.__CUSTOMERS__ = @json($customers->toArray());
    window.__ENTRIES__ = @json($entries->toArray());
    window.__STORE_URL__ = "{{ route('grn.store') }}";
  </script>
</body>
</html>
