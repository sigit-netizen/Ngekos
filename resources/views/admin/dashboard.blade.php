@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Stats Card 1 -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500">
                    <i class="fas fa-users fa-lg"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm font-medium">Total Users</h3>
                    <p class="text-2xl font-bold text-gray-800">1,250</p>
                </div>
            </div>
        </div>

        <!-- Stats Card 2 -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500">
                    <i class="fas fa-chart-line fa-lg"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm font-medium">Revenue</h3>
                    <p class="text-2xl font-bold text-gray-800">Rp 45.000.000</p>
                </div>
            </div>
        </div>

        <!-- Stats Card 3 -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                    <i class="fas fa-box-open fa-lg"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm font-medium">Products</h3>
                    <p class="text-2xl font-bold text-gray-800">320</p>
                </div>
            </div>
        </div>

        <!-- Stats Card 4 -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-500">
                    <i class="fas fa-exclamation-triangle fa-lg"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-gray-500 text-sm font-medium">Issues</h3>
                    <p class="text-2xl font-bold text-gray-800">12</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Table Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Recent Users</h2>
            <button class="px-4 py-2 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition-colors">
                View All
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Name
                        </th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Role
                        </th>
                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full border border-gray-300" src="https://ui-avatars.com/api/?name=John+Doe&background=random" alt="Avatar">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">John Doe</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-500">
                            john@example.com
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-500">
                            Admin
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 border-b border-gray-200">
                            <div class="flex items-center">
                                <img class="w-8 h-8 rounded-full border border-gray-300" src="https://ui-avatars.com/api/?name=Jane+Smith&background=random" alt="Avatar">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">Jane Smith</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-500">
                            jane@example.com
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200 text-sm text-gray-500">
                            User
                        </td>
                        <td class="px-6 py-4 border-b border-gray-200">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Offline
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
