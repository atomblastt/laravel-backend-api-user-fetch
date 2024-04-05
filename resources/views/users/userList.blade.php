
@extends('users/userLayout')

@section('content')
    <div class="row">
        <div class="col-6 bg-light rounded">
            <br>
            <h4> {{ $users['title'] }} </h4>
            <br>
            <table id="" class="table table-striped userTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users['data'] as $user)   
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$user->full_name}}</td>
                            <td>{{$user->age}}</td>
                            <td>{{ucfirst($user->gender)}}</td>
                            <td>{{$user->created_at->format('d F Y H:i:s')}}</td>
                            <td>
                                {{-- <button class="user-personal-delete btn btn-danger" data-id="{{ $user->id }}" data-name="{{ $user->full_name }}" data-bs-toggle="modal" data-bs-target="#userPersonalDeleteModal">Delete</button> --}}
                                <button class="btn btn-danger" onclick="confirmDelete('{{ encrypt($user->id) }}', '{{ $user->full_name }}')">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-1"></div>
        <div class="col-5 bg-light rounded">
            <br>
            <h4> {{ $daily_records['title'] }} </h4>
            <br>
            <table id="" class="table table-striped userTable" style="width:100%">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Date</th>
                        <th>Male Avg Count</th>
                        <th>Female Avg Count</th>
                        <th>Male Avg Age</th>
                        <th>Female Avg Age</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daily_records['data'] as $dailyRecord)   
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{date('d F Y', strtotime($dailyRecord->date))}}</td>
                            <td>{{$dailyRecord->male_count}}</td>
                            <td>{{$dailyRecord->female_count}}</td>
                            <td>{{$dailyRecord->male_avg_age}}</td>
                            <td>{{$dailyRecord->female_avg_age}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection