<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <script src="https://kit.fontawesome.com/0ca95d7137.js" crossorigin="anonymous"></script>

    @vite('resources/css/app.css')
    <title>Messenger</title>
</head>

<body>

    @if (isset($target_user))
        <div id="topup_form"
            class="bg-black bg-opacity-70 w-screen h-screen fixed z-30 top-0 left-0 flex items-center justify-center hidden">
            <form action="{{ route('topup') }}" method="post"
                class="bg-white w-4/5 md:w-96 lg:w-96 py-7 px-5 rounded-lg">
                @csrf
                <h2 class="font-light text-gray-500 text-md mb-3">Topup to {{ $target_user->name }}</h2>
                <input name="user_id" type="text" value="{{ $target_user->id }}" hidden>
                <input name="admin_id" type="text" value="{{ Auth::guard('admin')->user()->id }}" hidden>
                <input name="coin_count" type="number" class="w-full h-10 my-3 px-2 bg-gray-200 text-sm"
                    placeholder="Coin Count" required>
                <input name="price" type="number" class="w-full h-10 my-3 px-2 bg-gray-200 text-sm"
                    placeholder="Price" required>
                <select name="currency" class="w-full h-10 my-3 px-2 bg-gray-200 text-sm" required>
                    <option value="mmk">Myanmar Kyat</option>
                    <option value="baht">Thai Baht</option>
                </select>

                <div class="w-full flex items-center">
                    <div id="hide_topup_form"
                        class="w-full h-11 my-3 px-2 bg-red-500 text-white text-sm mr-3 flex items-center justify-center cursor-pointer">
                        <span>Cancel</span></div>
                    <button class="w-full h-11 my-3 px-2 bg-green-600 text-white text-sm">Topup</button>
                </div>
            </form>
        </div>

        {{-- <div id="take_out_form" class="bg-black bg-opacity-70 w-screen h-screen fixed z-30 top-0 left-0 flex items-center justify-center hidden">
            <form action="{{route('take_out')}}" method="post" class="bg-white w-4/5 md:w-96 lg:w-96 py-7 px-5 rounded-lg">
                @csrf
                <h2 class="font-light text-gray-500 text-md mb-3">Take Out Coins from {{$target_user->name}}</h2>
                <input name="user_id" type="text" value="{{$target_user->id}}" hidden>
                <input name="admin_id" type="text" value="{{Auth::guard('admin')->user()->id}}" hidden>
                <input name="coin_count" type="number" class="w-full h-10 my-3 px-2 bg-gray-200 text-sm" placeholder="Coin Count" required>

                <div class="w-full flex items-center">
                    <div id="hide_take_out_form" class="w-full h-11 my-3 px-2 bg-gray-300 text-gray-700 text-sm mr-3 flex items-center justify-center cursor-pointer"><span>Cancel</span></div>
                    <button class="w-full h-11 my-3 px-2 bg-orange-500 text-white text-sm">Take Out</button>
                </div>
            </form>
        </div> --}}
    @endif

    <div class="w-screen h-screen flex flex-row">
        <div id="user_list_container"
            class="h-screen lg:w-2/6 w-full lg:relative fixed z-20 lg:flex hidden bg-white flex-col px-2 overflow-y-scroll py-5">
            <div class="w-full flex flex-row items-center pb-5 pl-3">
                <a href="/nova/dashboards/main"><i
                        class="fa-sharp fa-solid fa-chevron-left text-2xl text-gray-600"></i></a>
                <input id="search_bar" type="search" placeholder="Search"
                    class="h-10 px-2 flex-grow bg-gray-200 ml-3 rounded-md" name="search_user" id="">
                <div id="search_btn" class="cursor-pointer ml-3 px-3 py-2">
                    <i class="fa-solid fa-magnifying-glass text-lg"></i>
                </div>
            </div>
            <div id="user_list" class="flex flex-col">

            </div>
            <div id="get_more_user_btn"
                class="w-full py-3 text-sm cursor-pointer mb-32 mt-10 bg-gray-200 flex justify-center items-center">
                <span>Load More</span>
            </div>
        </div>


        @if (isset($target_user))
            <div class="flex flex-col lg:w-4/6 w-full bg-gray-200">
                <div class="w-full h-16 bg-white flex flex-row items-center px-5">
                    <div id="show_user_list" class="h-16 w-16 lg:hidden flex flex-col justify-center items-center">
                        <i class="fa-sharp fa-solid fa-chevron-left text-2xl text-gray-600"></i>
                    </div>
                    <div class="h-16"></div>
                    <div class="flex flex-col">
                        <div class="flex flex-row items-center">
                            <span class="text-md">{{ $target_user->name }}</span>
                            <span class="ml-3 mr-3 text-gray-400">|</span>
                            <span class="text-sm text-green-700">{{ $target_user->main_wallet->balance }} MMK</span>
                        </div>
                        <span class="text-xs text-gray-600 mt-2">{{ $target_user->phone }}</span>
                    </div>

                    <div class="flex-grow"></div>

                    {{-- <div class="flex flex-col md:flex-row lg:flex-row justify-center items-center">
                        <div id="show_topup_form" class="bg-green-600 px-5 py-1 ml-3 rounded-full flex flex-row items-center cursor-pointer">
                            <i class="fa-solid fa-comment-dollar text-white"></i>
                            <span class="text-sm ml-1 text-white hidden md:flex lg:flex">Topup</span>
                        </div>
                        <div class="h-1"></div>
                        <div id="show_take_out_form" class="bg-orange-500 px-5 py-1 ml-3 rounded-full flex flex-row items-center cursor-pointer">
                            <i class="fa-solid fa-rotate-left text-white"></i>
                            <span class="text-sm ml-1 text-white hidden md:flex lg:flex">Take Out</span>
                        </div>
                    </div> --}}
                </div>

                <div id="message_container" class="flex-grow overflow-y-scroll">
                    @foreach ($target_user->messages as $message)
                        @if ($message->admin_id != null)
                            @if ($message->image_location)
                                <div class="w-full flex flex-col items-end my-4 justify-center px-5">
                                    <div class="bg-gray-200 lg:w-2/5 md:w-1/2 w-2/3 rounded-2xl overflow-hidden">
                                        <img src="{{ env('DO_STORAGE_URL') . $message->image_location }}" class="w-full"
                                            alt="">
                                    </div>
                                    <span
                                        class="text-xs text-cyan-700 ml-2">{{ $message->created_at->format('M-d h:i') }}</span>
                                </div>
                            @else
                                <div class="w-full flex flex-row items-center my-4 justify-end px-5">
                                    <span
                                        class="py-4 max-w-md bg-teal-600 text-white px-4 text-sm rounded-tr-2xl rounded-tl-2xl rounded-bl-2xl flex flex-col items-start">
                                        <span>{{ $message->message }}</span>
                                        <span
                                            class="text-xs text-white mt-2">{{ $message->created_at->format('M-d h:i') }}</span>
                                    </span>
                                </div>
                            @endif
                        @else
                            @if ($message->image_location)
                                <div class="w-full flex flex-col items-start my-4 justify-center px-5">
                                    <div class="bg-gray-200 lg:w-2/5 md:w-1/2 w-2/3 rounded-2xl overflow-hidden">
                                        <img src="{{ env('DO_STORAGE_URL') . $message->image_location }}" class="w-full"
                                            alt="">
                                    </div>
                                    <span
                                        class="text-xs text-cyan-700 ml-2">{{ $message->created_at->format('M-d h:i') }}</span>
                                </div>
                            @else
                                <div class="w-full flex flex-row items-center my-4 justify-start px-5">
                                    <div
                                        class="bg-gray-300 max-w-md py-4 px-4 text-sm rounded-tr-2xl rounded-tl-2xl rounded-br-2xl flex flex-col items-start">
                                        <span>{{ $message->message }}</span>
                                        <span
                                            class="text-xs text-cyan-700 mt-2">{{ $message->created_at->format('M-d h:i') }}</span>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>

                <div id="image_sender_container"
                    class="hidden lg:w-4/6 w-full h-screen bg-black bg-opacity-50 flex-col items-center justify-end fixed bottom-0 z-10">
                    <form id="image_form" action="{{ route('admin_send_image', ['user_id' => $target_user->id]) }}"
                        method="post" class="-mb-10 w-full flex flex-col rounded-2xl pb-16 pt-12 items-center bg-white"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="w-56 h-56 bg-gray-100 bg-cover bg-center rounded-md mt-5 mb-5 flex flex-col justify-center items-center hover:cursor-pointer"
                            id="image_preview">
                            <i class="fa-solid fa-image text-2xl text-gray-400"></i>
                            <span class="text-sm text-gray-500">Select Image</span>
                        </div>
                        <input type='file' id="image_selector" name="image" accept="image/*" hidden />
                        <div class="flex flex-row">
                            <div id="cancel_image_btn"
                                class="h-8 w-20 text-sm flex justify-center items-center cursor-pointer text-white rounded-md bg-red-500">
                                Cancel</div>
                            <button id="send_image_btn"
                                class="h-8 w-20 text-sm flex justify-center items-center cursor-pointer ml-5 text-white rounded-md bg-sky-500 opacity-50"
                                disabled>Send</button>
                        </div>
                    </form>
                </div>

                @if ($target_user->topups->last())
                    @if ($target_user->topups->last()->success == false)
                        <div class="w-full py-3 px-5 flex flex-col">
                            <div
                                class="w-full flex lg:justify-between md:justify-between lg:flex-row md:flex-row flex-col lg:items-center md:items-center bg-white py-3 px-3 text-sm border-l-4 border-red-400">
                                <div class="flex flex-row items-center">
                                    <span class="text-blue-900">{{ $target_user->name }}</span>&nbsp; မှငွေဖြည့်သွင်းရန်တောင်းဆိုထားပါသည်
                                    &nbsp;
                                    <span
                                        class="text-orange-600 font-bold">{{ $target_user->topups->last()->coin_count }}</span>
                                    &nbsp; MMK
                                </div>
                                <div class="flex flex-row items-center lg:mt-0 md:mt-0 mt-5">
                                    <a class="bg-blue-500 text-white text-sm px-3 py-1 rounded-full"
                                        href="/zadmin/resources/top-ups/{{ $target_user->topups->last()->id }}">view</a>
                                    <a class="bg-green-500 ml-2 text-sm text-white px-3 py-1 rounded-full"
                                        href="/topup/approve/{{ $target_user->topups->last()->id }}">approve</a>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="w-full bg-white h-14 flex-row flex items-center px-5">
                    <i id="want_to_send_image"
                        class="fa-sharp cursor-pointer fa-solid fa-image text-gray-500 text-2xl"></i>
                    <input type="text" id="message_input" placeholder="write a message"
                        class="h-14 ml-5 text-sm flex-grow focus:outline-none">
                    <div id="send_message_btn" class="cursor-pointer h-14 w-14 flex justify-center items-center"><i
                            class="fa fa-paper-plane text-xl text-gray-700" aria-hidden="true"></i></div>
                </div>
            </div>
        @else
            <div class="flex-grow flex justify-center items-center">
                <span class="text-gray-500 text-sm">Select a chat to start messaging</span>
            </div>
        @endif
    </div>

    <script src="https://code.jquery.com/jquery-3.6.2.js" integrity="sha256-pkn2CUZmheSeyssYw3vMp1+xyub4m+e+QK4sQskvuo4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"
        integrity="sha384-/KNQL8Nu5gCHLqwqfQjA689Hhoqgi2S84SNUxC3roTe4EhJ9AfLkp8QiQcU8AMzI" crossorigin="anonymous">
    </script>


    <!-- User List -->
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        //Socket Start
        let ip_address = "{{ env('SOCKET_IP') }}";
        let socket_port = "{{ env('SOCKET_PORT') }}";
        // let socket = io(ip_address + ':' + socket_port);
        // let socket = io("{{ env('SOCKET_ADDRESS') }}",{
        //   path: '/mysocket'
        // });
        let socket = io.connect({
            path: '/mysocket'
        });
        //On connect
        socket.on('connect', function() {
            socket.emit('admin_connection');
        });


        $(document).ready(function() {

            //Topup
            $("#show_topup_form").on("click", function() {
                $("#topup_form").removeClass("hidden");
            });

            $("#hide_topup_form").on("click", function() {
                $("#topup_form").addClass("hidden");
            });

            //Take Out
            $("#show_take_out_form").on("click", function() {
                $("#take_out_form").removeClass("hidden");
            });

            $("#hide_take_out_form").on("click", function() {
                $("#take_out_form").addClass("hidden");
            });




            getUsers("{{ env('API_BASE_URL') }}/messenger/users");

            //When a user connect
            // socket.on('user_join_chatroom',(user_id)=>{
            //     // console.log(data);
            //     // $(".user_active_status").removeClass('bg-green-500');
            //     console.log(user_id+" Joined the Chat");
            //     $("#active_status_"+user_id).addClass('bg-green-500');
            // });

            // socket.on('user_leave_chatroom',(user_id)=>{
            //     console.log(user_id+" is Leaving");
            //     $("#active_status_"+user_id).removeClass('bg-green-500');
            // });


            //Put New Message User to top of list
            socket.on("admin:private-channel:App\\Events\\PrivateMessageEvent", (message) => {
                $("#user_selector_" + message.new_message.user_id).remove();
                var user_new_selector = '<a href="/messenger?to=' + message.new_message.user_id + '">';
                user_new_selector += '<div id="user_selector_' + message.new_message.user_id +
                    '" class="w-full my-1 flex flex-row justify-start items-center py-3 px-3 rounded-md hover:bg-gray-100 hover:cursor-pointer">';
                user_new_selector += '<div style="background-image: url(' + message.new_message
                    .profile_picture +
                    ');" class="rounded-full h-14 w-14 bg-gray-500 bg-cover bg-center"></div>';
                user_new_selector += '<div class="flex flex-col flex-grow">';
                user_new_selector += '<div class="flex flex-row justify-between items-center">';
                user_new_selector += '<div class="flex flex-row items-center">';
                user_new_selector += '<span class="ml-3 text-gray-800 text-sm"> ' + message.new_message
                    .name + ' </span>';
                user_new_selector += '@if (isset($target_user))';
                if ("{{ $target_user->id }}" != message.new_message.user_id) {
                    user_new_selector +=
                        '<i class="fa-solid fa-message ml-2 mt-1 text-green-600 text-md"></i>';
                }
                user_new_selector += '@else';
                user_new_selector += '<i class="fa-solid fa-message ml-2 mt-1 text-green-600 text-md"></i>';
                user_new_selector += '@endif';
                user_new_selector += '</div>';
                user_new_selector += '<span class="ml-3 text-gray-800 text-xs font-light"> ' + message
                    .new_message.message_date + ' </span>';
                user_new_selector += '</div>';
                user_new_selector += '<div class="flex flex-row justify-between items-center">';
                user_new_selector += '<span class="ml-3 text-gray-800 text-xs"> ' + message.new_message
                    .message + ' </span>';
                user_new_selector += '<span class="ml-3 text-gray-800 text-xs font-light"> ' + message
                    .new_message.message_time + ' </span>';
                user_new_selector += '</div>';
                user_new_selector += '</div>';
                user_new_selector += '</div>';
                user_new_selector += '</a>';

                $("#user_list").prepend(user_new_selector);
            });

            $("#search_btn").on("click", function() {
                var search_keyword = $("#search_bar").val();
                $("#user_list").empty();
                getUsers("{{ env('API_BASE_URL') }}/messenger/users?search=" + search_keyword);
            });

        });

        $("#get_more_user_btn").on("click", function() {
            getMoreUsers();
        });

        var next_page;

        function getUsers(get_user_url) {
            $.get(get_user_url,
                function(data, status) {
                    if (status == "success") {
                        if (data.next_page_url == null || data.next_page_url == "") {
                            $("#get_more_user_btn").remove();
                        }
                        next_page = data.next_page_url;
                        data["data"].forEach(element => {
                            var user_selector = '<a href="/messenger?to=' + element.id + '">';
                            user_selector += '<div id="user_selector_' + element.id +
                                '" class="w-full my-1 flex flex-row justify-start items-center py-3 px-3 rounded-md hover:bg-gray-100 hover:cursor-pointer">';
                            user_selector += '<div style="background-image: url(' + element.profile_picture +
                                ');" class="rounded-full h-14 w-14 bg-gray-500 bg-cover bg-center"></div>';
                            user_selector += '<div class="flex flex-col flex-grow">';
                            user_selector += '<div class="flex flex-row justify-between items-center">';
                            user_selector += '<div class="flex flex-row items-center">';
                            user_selector += '<span class="ml-3 text-gray-800 text-sm"> ' + element.name +
                                ' </span>';
                            if (element.active == true) {
                                user_selector +=
                                    '<i class="fa-solid fa-message ml-2 mt-1 text-green-600 text-md"></i>';
                            }
                            user_selector += '</div>';
                            user_selector += '<span class="ml-3 text-gray-800 text-xs font-light"> ' + element
                                .message_date + ' </span>';
                            user_selector += '</div>';
                            user_selector += '<div class="flex flex-row justify-between items-center">';
                            user_selector += '<span class="ml-3 text-gray-800 text-xs"> ' + element
                                .last_message + ' </span>';
                            user_selector += '<span class="ml-3 text-gray-800 text-xs font-light"> ' + element
                                .message_time + ' </span>';
                            user_selector += '</div>';
                            user_selector += '</div>';
                            user_selector += '</div>';
                            user_selector += '</a>';

                            $("#user_list").append(user_selector);
                        });
                    } else {
                        alert("Some thing went wrong");
                    }
                });
        }

        function getMoreUsers() {
            console.log(next_page);
            if (next_page == null || next_page == "") {
                $("#get_more_user_btn").remove();
                return;
            }
            $.get(next_page,
                function(data, status) {
                    if (status == "success") {
                        next_page = data.next_page_url;
                        data["data"].forEach(element => {
                            var user_selector = '<a href="/messenger?to=' + element.id + '">';
                            user_selector += '<div id="user_selector_' + element.id +
                                '" class="w-full my-1 flex flex-row justify-start items-center py-3 px-3 rounded-md hover:bg-gray-100 hover:cursor-pointer">';
                            user_selector += '<div style="background-image: url(' + element.profile_picture +
                                ');" class="rounded-full h-14 w-14 bg-gray-500 bg-cover bg-center"></div>';
                            user_selector += '<div class="flex flex-col flex-grow">';
                            user_selector += '<div class="flex flex-row justify-between items-center">';
                            user_selector += '<div class="flex flex-row items-center">';
                            user_selector += '<span class="ml-3 text-gray-800 text-sm"> ' + element.name +
                                ' </span>';
                            if (element.active == true) {
                                user_selector +=
                                    '<i class="fa-solid fa-message ml-2 mt-1 text-green-600 text-md"></i>';
                            }
                            user_selector += '</div>';
                            user_selector += '<span class="ml-3 text-gray-800 text-xs font-light"> ' + element
                                .message_date + ' </span>';
                            user_selector += '</div>';
                            user_selector += '<div class="flex flex-row justify-between items-center">';
                            user_selector += '<span class="ml-3 text-gray-800 text-xs"> ' + element
                                .last_message + ' </span>';
                            user_selector += '<span class="ml-3 text-gray-800 text-xs font-light"> ' + element
                                .message_time + ' </span>';
                            user_selector += '</div>';
                            user_selector += '</div>';
                            user_selector += '</div>';
                            user_selector += '</a>';

                            $("#user_list").append(user_selector);
                        });
                    } else {
                        alert("Some thing went wrong");
                    }
                });
        }
    </script>
    <!-- User Lis End -->

    @if (isset($target_user))
        <script>
            $(document).ready(function() {

                let target_user = "{{ $target_user->id }}";
                //scroll bottom
                var message_container = $('#message_container');
                message_container.scrollTop(message_container.prop("scrollHeight"));
                setTimeout(function() {
                    message_container.scrollTop(message_container.prop("scrollHeight"));
                }, 1000);

                $("#send_message_btn").on("click", function() {
                    sendMessage();
                });

                $(document).on('keypress', function(e) {
                    if (e.which == 13) {
                        sendMessage();
                    }
                });

                function sendMessage() {
                    var new_message = $("#message_input").val();
                    if (new_message == null || new_message == "") {
                        return;
                    }

                    $.post("{{ env('API_BASE_URL') }}/admin/message/send", {
                            message: new_message,
                            user_id: '{{ $target_user->id }}',
                            _token: "{{ csrf_token() }}"
                        },
                        function(data, status) {
                            // console.log(data.data.new_message.message);
                            if (status == "success") {
                                if (data.success == true) {
                                    var new_message_ui =
                                        '<div class="w-full flex flex-row items-center my-4 justify-end px-5">';
                                    new_message_ui +=
                                        '<div class="py-4 max-w-md bg-teal-600 text-white px-4 text-sm rounded-tr-2xl rounded-tl-2xl rounded-bl-2xl flex flex-col items-start">';
                                    new_message_ui += '<span>' + data.data.new_message.message + '</span>';
                                    new_message_ui += '<span class="text-xs text-white mt-2">now</span>';
                                    new_message_ui += '</div></div>';
                                    message_container.append(new_message_ui);
                                    message_container.scrollTop(message_container.prop("scrollHeight"));

                                    console.log(data);

                                    //Put new message to top
                                    $("#user_selector_" + data.data.new_message.user_id).remove();
                                    var user_new_selector = '<a href="/messenger?to=' + data.data.new_message
                                        .user_id + '">';
                                    user_new_selector += '<div id="user_selector_' + data.data.new_message.user_id +
                                        '" class="w-full my-1 flex flex-row justify-start items-center py-3 px-3 rounded-md hover:bg-gray-100 hover:cursor-pointer">';
                                    user_new_selector += '<div style="background-image: url(' + data.data
                                        .new_message.profile_picture +
                                        ');" class="rounded-full h-14 w-14 bg-gray-500 bg-cover bg-center"></div>';
                                    user_new_selector += '<div class="flex flex-col flex-grow">';
                                    user_new_selector += '<div class="flex flex-row justify-between items-center">';
                                    user_new_selector += '<div class="flex flex-row items-center">';
                                    user_new_selector += '<span class="ml-3 text-gray-800 text-sm"> ' + data.data
                                        .new_message.name + ' </span>';
                                    user_new_selector += '@if (isset($target_user))';
                                    if ("{{ $target_user->id }}" != data.data.new_message.user_id) {
                                        user_new_selector +=
                                            '<i class="fa-solid fa-message ml-2 mt-1 text-green-600 text-md"></i>';
                                    }
                                    user_new_selector +=
                                        '@else';
                                    user_new_selector +=
                                        '<i class="fa-solid fa-message ml-2 mt-1 text-green-600 text-md"></i>';
                                    user_new_selector +=
                                    '@endif';
                                    user_new_selector += '</div>';
                                    user_new_selector += '<span class="ml-3 text-gray-800 text-xs font-light"> ' +
                                        data.data.new_message.message_date + ' </span>';
                                    user_new_selector += '</div>';
                                    user_new_selector += '<div class="flex flex-row justify-between items-center">';
                                    user_new_selector += '<span class="ml-3 text-gray-800 text-xs"> ' + data.data
                                        .new_message.message + ' </span>';
                                    user_new_selector += '<span class="ml-3 text-gray-800 text-xs font-light"> ' +
                                        data.data.new_message.message_time + ' </span>';
                                    user_new_selector += '</div>';
                                    user_new_selector += '</div>';
                                    user_new_selector += '</div>';
                                    user_new_selector += '</a>';

                                    $("#user_list").prepend(user_new_selector);


                                }
                            } else {
                                alert("Some thing went wrong");
                            }
                        });
                    $("#message_input").val('');
                }


                $("#show_user_list").on("click", function() {
                    $("#user_list_container").removeClass('hidden');
                });

                //Image
                $("#want_to_send_image").on("click", function() {
                    $("#image_sender_container").removeClass('hidden');
                    $("#image_sender_container").addClass('flex');
                });

                //Image
                $("#cancel_image_btn").on("click", function() {
                    $("#image_sender_container").removeClass('flex');
                    $("#image_sender_container").addClass('hidden');
                });


                //Image Preview
                $("#image_preview").on("click", function() {
                    $("#image_selector").click();
                });

                function readURL(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            $('#image_preview').css("background-image", "url(" + e.target.result + ")");
                            $('#image_preview').html("");
                        }

                        reader.readAsDataURL(input.files[0]);
                    }
                    $("#send_image_btn").removeClass("opacity-50");
                    $("#send_image_btn").prop("disabled", false);
                }

                $("#image_selector").change(function() {
                    readURL(this);
                });


                //On Message Receive
                socket.on("admin:private-channel:App\\Events\\PrivateMessageEvent", (message) => {
                    //Update Chat
                    if (message.new_message.user_id == target_user) {
                        if (message.new_message.image_location) {
                            var new_message_ui =
                                '<div class="w-full flex flex-col items-start my-4 justify-center px-5">';
                            new_message_ui +=
                                '<div class="bg-green-400 lg:w-2/5 md:w-1/2 w-2/3 rounded-2xl overflow-hidden">';

                            new_message_ui += '<img src="{{ env('DO_STORAGE_URL') }}' + message.new_message
                                .image_location + '" class="w-full" alt="">';

                            new_message_ui += '</div><span class="text-xs text-cyan-700 ml-2">now</span></div>';
                            message_container.append(new_message_ui);
                            message_container.scrollTop(message_container.prop("scrollHeight"));
                        } else {
                            var new_message_ui =
                                '<div class="w-full flex flex-row items-center my-4 justify-start px-5">';
                            new_message_ui +=
                                '<div class="bg-gray-300 max-w-md py-4 px-4 text-sm rounded-tr-2xl rounded-tl-2xl rounded-br-2xl flex flex-col items-start">';
                            new_message_ui += '<span>' + message.new_message.message + '</span>';
                            new_message_ui += '<span class="text-xs text-cyan-700 mt-2">now</span>';
                            new_message_ui += '</div></div>';
                            message_container.append(new_message_ui);
                            message_container.scrollTop(message_container.prop("scrollHeight"));
                        }
                        setTimeout(function() {
                            message_container.scrollTop(message_container.prop("scrollHeight"));
                        }, 1000);

                        $.post("{{ env('API_BASE_URL') }}/messenger/user/" + target_user + "/read");

                    }
                });

            });
        </script>
    @else
        <script>
            $(document).ready(function() {
                $("#user_list_container").removeClass('hidden');
            });
        </script>
    @endif


</body>

</html>
