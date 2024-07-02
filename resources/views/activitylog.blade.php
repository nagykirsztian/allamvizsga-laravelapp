<head>
    <script>
window.onload = function() {
            @if(session('success') || session('error'))
                alert("{{ session('success') ?? session('error') }}");
            @endif
        }
    </script>
</head>

<<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Downloadable') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('sensor.downloadAll') }}">
                        <div>
                            <label for="sensor_id">Sensor ID:</label>
                            <input type="text" id="sensor_id" name="sensor_id" required>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-black font-bold py-2 px-4 rounded">
                                Download All Data
                            </button>
                        </div>
                    </form>

                    <form method="GET" action="{{ route('sensor.downloadByDate') }}" class="mt-6">
                        <div>
                            <label for="sensor_id_date">Sensor ID:</label>
                            <input type="text" id="sensor_id_date" name="sensor_id" required>
                        </div>
                        <div>
                            <label for="start_date">Start Date:</label>
                            <input type="date" id="start_date" name="start_date" required>
                        </div>
                        <div>
                            <label for="end_date">End Date:</label>
                            <input type="date" id="end_date" name="end_date" required>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-black font-bold py-2 px-4 rounded">
                                Download Data by Date
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
