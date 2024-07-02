<head>
    <script>
window.onload = function() {
            @if(session('success') || session('error'))
                alert("{{ session('success') ?? session('error') }}");
            @endif
        }
    </script>
</head>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Senosrs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> 
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">  
                
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                
                    <th scope="col" class="px-6 py-3">
                        Editor of the year
                    </th>
                    
                </tr>
            </thead>
            <tbody id="sensorDataBody" >
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4">
                <h1 class="card-title">Add Sensor</h1>
                <form method="POST" action="{{ route('editsensors.store') }}">
                        @csrf
                        <input type="hidden" name="action" value="addsensor">

                        <div class="form-group">
                            <label for="id">ID</label>
                            <input type="text" class="form-control" id="id" name="id" required>
                        </div>

                        <div class="form-group">
                            <label for="port">Port</label>
                            <input type="number" class="form-control" id="port" name="port" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4">
                <h5 class="card-title">Delete Sensor</h5>
                    <form method="POST" action="{{ route('editsensors.store') }}">
                        @csrf
                       
                        <input type="hidden" name="action" value="destroy">

                        <div class="form-group">
                            <label for="delete-id">Sensor ID to delete</label>
                            <input type="text" class="form-control" id="delete-id" name="delete_id" required>
                        </div>
                        
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </form>
                </td>
                </tr>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                <td class="px-6 py-4">
                <h5 class="card-title">Set Min/Max thresholds</h5>
                <form method="POST" action="{{ route('editsensors.store') }}">
                    @csrf
                    <input type="hidden" name="action" value="thresholds">
                    <div class="form-group">
                        <label for="sensor-id">Sensor ID</label>
                        <input type="text" class="form-control" id="sensor-id" name="id" required>
                    </div>
                    <div class="form-group">
                        <label for="min-value">Minimum Value</label>
                        <input type="text" class="form-control" id="min-value" name="min" pattern="^[0-9]+(\.[0-9]+)?E\-[0-9]+$" required>
                    </div>
                    <div class="form-group">
                        <label for="max-value">Maximum Value</label>
                        <input type="text" class="form-control" id="max-value" name="max" pattern="^[0-9]+(\.[0-9]+)?E\-[0-9]+$" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>

            </tbody>
        </table>
    </div>

                </div> 
            </div>
        </div>
    </div> 
</x-app-layout>