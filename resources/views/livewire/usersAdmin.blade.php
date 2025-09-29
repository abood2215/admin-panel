@extends('layouts.admin')

@section('title', 'Users')

@section('content')
  <div class="overflow-x-auto">
    <table class="min-w-full bg-neutral-900 text-left text-sm text-neutral-200 rounded-lg overflow-hidden shadow-lg">
      <thead class="bg-neutral-800 text-xs uppercase text-neutral-400">
        <tr>
          <th class="px-4 py-3">#</th>
          <th class="px-4 py-3">Name</th>
          <th class="px-4 py-3">Email</th>
          <th class="px-4 py-3">Role</th>
          <th class="px-4 py-3">Registered At</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $i => $user)
          <tr class="{{ $i % 2 === 0 ? 'bg-neutral-900' : 'bg-neutral-800' }} hover:bg-neutral-700">
            <td class="px-4 py-3">{{ $i + 1 }}</td>
            <td class="px-4 py-3 font-medium text-neutral-100">{{ $user->name }}</td>
            <td class="px-4 py-3 text-neutral-300">{{ $user->email }}</td>
            <td class="px-4 py-3">
              @if($user->is_admin)
                <span class="inline-block px-2 py-1 bg-yellow-600 text-neutral-900 rounded-full text-xs">Admin</span>
              @else
                <span class="inline-block px-2 py-1 bg-blue-500 text-white rounded-full text-xs">User</span>
              @endif
            </td>
            <td class="px-4 py-3 text-neutral-400">{{ $user->created_at->format('Y-m-d') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
