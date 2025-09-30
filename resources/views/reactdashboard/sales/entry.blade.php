<!doctype html>
<html>
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Sales Entry</title>
 @vite(['resources/css/app.css', 'resources/js/app.js'])
  @viteReactRefresh
  @vite(['resources/js/app.jsx'])
</head>
<body>
    <div>
        <div id="salesApp"></div>
  </div>

 <script>
    // pass server data to React via window.* variables
    window.__INITIAL_SALES__ = @json($sales->toArray());
    window.__CUSTOMERS__ = @json($customers->toArray());
    window.__ENTRIES__ = @json($entries->toArray());
    window.__ITEMS__ = @json($items->toArray());  // <--- ADD THIS LINE
    window.__STORE_URL__ = "{{ route('grn.store') }}";
    window.__PRINTED_SALES__ = {!! json_encode($printedSales) !!};
    window.__UNPRINTED_SALES__ = {!! json_encode($unprintedSales) !!};
</script>
</body>
</html>