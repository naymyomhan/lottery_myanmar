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
    

    <div class="w-screen h-screen flex flex-col">
        <div id="message_container" class="w-full bg-gray-100 h-full flex-grow overflow-y-scroll">
            @foreach($messages as $message)
                @if($message->admin_id!=null)
                    @if($message->image_location)
                        <div class="w-full flex flex-col items-start my-4 justify-center px-5">
                            <div class="bg-gray-100 lg:w-2/5 md:w-1/2 w-2/3 rounded-2xl overflow-hidden">
                                <img src="{{env('DO_STORAGE_URL').$message->image_location}}" class="w-full" alt="">
                            </div>
                            <span class="text-xs text-cyan-700 ml-2">{{$message->created_at->format('M-d h:i')}}</span>
                        </div>
                    @else
                        <div class="w-full flex flex-col items-start justify-center px-5 my-5">
                            <span class="text-xs bg-gray-300 px-5 py-3 rounded-tr-2xl rounded-tl-2xl rounded-br-2xl">{{$message->message}}</span>
                            <span class="text-xs font-light text-gray-400">{{$message->created_at->format('M-d h:i')}}</span>
                        </div>
                    @endif
                @else
                    @if($message->image_location)
                        <div class="w-full flex flex-col items-end my-4 justify-center px-5">
                            <div class="bg-gray-100 lg:w-2/5 md:w-1/2 w-2/3 rounded-2xl overflow-hidden">
                                <img src="{{env('DO_STORAGE_URL').$message->image_location}}" class="w-full" alt="">
                            </div>
                            <span class="text-xs text-cyan-700 ml-2">{{$message->created_at->format('M-d h:i')}}</span>
                        </div>
                    @else
                        <div class="w-full flex flex-col items-end justify-center px-5 my-5">
                            <span class="text-xs bg-sky-500 px-5 py-3 text-white rounded-tr-2xl rounded-tl-2xl rounded-bl-2xl">{{$message["message"]}}</span>
                            <span class="text-xs font-light text-gray-400">{{$message->created_at->format('M-d h:i')}}</span>
                        </div>
                    @endif
                    
                @endif
            @endforeach
        </div>

        <div id="image_sender_container" class="hidden w-full h-screen bg-black bg-opacity-50 flex-col items-center justify-end fixed bottom-0 z-10">
            <form id="image_form" action="{{route('user_send_image',['bearer_token'=>$bearer_token])}}" method="post" class="-mb-10 w-full flex flex-col rounded-2xl pb-16 pt-12 items-center bg-white" enctype="multipart/form-data">
                @csrf
                <div class="w-56 h-56 bg-gray-100 bg-cover bg-center rounded-md mt-5 mb-5 flex flex-col justify-center items-center hover:cursor-pointer" id="image_preview">
                    <i class="fa-solid fa-image text-2xl text-gray-400"></i>
                    <span class="text-sm text-gray-500">Select Image</span>
                </div>
                <input type='file' id="image_selector" name="image" accept="image/*" hidden/>
                <div class="flex flex-row">
                    <div id="cancel_image_btn" class="h-8 w-20 text-sm flex justify-center items-center text-white rounded-md bg-red-500">Cancel</div>
                    <button id="send_image_btn" class="h-8 w-20 text-sm flex justify-center items-center ml-5 text-white rounded-md bg-sky-500 opacity-50" disabled>Send</button>
                </div>
            </form>
        </div>

        <div class="w-full bg-white h-14 flex-row flex items-center px-5">
            <i id="want_to_send_image" class="fa-sharp fa-solid fa-image text-gray-500 text-2xl"></i>
            <input type="text" id="message_input" placeholder="write a message" class="h-14 ml-5 text-sm flex-grow focus:outline-none">
            <div id="send_message_btn" class="cursor-pointer h-14 w-14 flex justify-center items-center"><i class="fa fa-paper-plane text-xl text-gray-700" aria-hidden="true"></i></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.2.js" integrity="sha256-pkn2CUZmheSeyssYw3vMp1+xyub4m+e+QK4sQskvuo4=" crossorigin="anonymous"></script>
    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js" integrity="sha384-/KNQL8Nu5gCHLqwqfQjA689Hhoqgi2S84SNUxC3roTe4EhJ9AfLkp8QiQcU8AMzI" crossorigin="anonymous"></script>

    @if(isset($user))
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            }
        });

        $(document).ready(function(){
            //scroll bottom
            var message_container = $('#message_container');
            message_container.scrollTop(message_container.prop("scrollHeight"));

            setTimeout(
                function() 
                {
                    message_container.scrollTop(message_container.prop("scrollHeight"));
                }, 1000);

            var tokenString="{{$bearer_token}}";

            // console.log(tokenString);

            $("#send_message_btn").on("click",function(){
                var new_message=$("#message_input").val();
                if(new_message==null || new_message==""){
                    return;
                }
                // console.log(new_message);

                $.ajax({
                    url: "{{env('API_BASE_URL')}}/api/message/send",
                    type: 'POST',
                    beforeSend: function(xhr, settings) { xhr.setRequestHeader('Authorization','Bearer ' + tokenString ); } ,
                    data: {
                        'message':new_message,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (data,status) { 
                        // console.log(data);
                        if(data.success==true){
                                var new_message_ui='<div class="w-full flex flex-col items-end justify-center px-5 my-5">';
                                new_message_ui+='<span class="text-xs bg-sky-500 px-5 py-3 text-white rounded-tr-2xl rounded-tl-2xl rounded-bl-2xl">';
                                new_message_ui+=data.data.new_message.message;
                                new_message_ui+='</span><span class="text-xs font-light text-gray-400">now</span></div>';
                                message_container.append(new_message_ui);
                                message_container.scrollTop(message_container.prop("scrollHeight"));
                            }
                    },
                    error: function (status) { 
                        console.log(status);
                    },
                });
                $("#message_input").val('');
            });


            //Image
            $("#want_to_send_image").on("click",function(){
                $("#image_sender_container").removeClass('hidden');
                $("#image_sender_container").addClass('flex');
            });

            //Image
            $("#cancel_image_btn").on("click",function(){
                $("#image_sender_container").removeClass('flex');
                $("#image_sender_container").addClass('hidden');
            });


            //Image Preview
            $("#image_preview").on("click",function(){
                $("#image_selector").click();
            });

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#image_preview').css("background-image", "url("+e.target.result+")");
                        $('#image_preview').html("");
                    }

                    reader.readAsDataURL(input.files[0]);
                }
                $("#send_image_btn").removeClass("opacity-50");
                $("#send_image_btn").prop("disabled", false);
            }

            $("#image_selector").change(function(){
                readURL(this);
            });

            
        });

        $(function(){
            let user_id="{{$user->id}}";
            //let socket = io("{{env('SOCKET_ADDRESS')}}",{
              // path: '/mysocket'
            //});
            let socket=io.connect({
                path: '/mysocket'
            });
            socket.on('connect',function(){
                
                socket.emit('user_connection',user_id);
            });

            socket.on('disconnect',function(){
                socket.emit('user_disconnect');
            });

            socket.on('updateAdminStatus',()=>{
                console.log("Admin is connected")
            });

            socket.on("private-channel:App\\Events\\PrivateMessageEvent",(message)=>{
                console.log(message.new_message.message);

                var message_container = $('#message_container');

                if(message.new_message.image_location){
                    var new_message_ui='<div class="w-full flex flex-col items-start my-4 justify-center px-5">';
                        new_message_ui+='<div class="bg-green-400 lg:w-2/5 md:w-1/2 w-2/3 rounded-2xl overflow-hidden">';
                            
                        new_message_ui+='<img src="{{env("DO_STORAGE_URL")}}'+ message.new_message.image_location +'" class="w-full" alt="">';

                        new_message_ui+='</div><span class="text-xs text-cyan-700 ml-2">';
                        new_message_ui+=message.new_message.created_at;
                        new_message_ui+='</span><span class="text-xs font-light text-gray-400">now</span></div>';
                        message_container.append(new_message_ui);
                        message_container.scrollTop(message_container.prop("scrollHeight"));
                }else{
                    var new_message_ui='<div class="w-full flex flex-col items-start justify-center px-5 my-5">';
                        new_message_ui+='<span class="text-xs bg-gray-300 px-5 py-3 rounded-tr-2xl rounded-tl-2xl rounded-br-2xl">';
                        new_message_ui+=message.new_message.message;
                        new_message_ui+='</span><span class="text-xs font-light text-gray-400">now</span></div>';
                        message_container.append(new_message_ui);
                        message_container.scrollTop(message_container.prop("scrollHeight"));
                        
                }

                setTimeout(
                function() 
                {
                    message_container.scrollTop(message_container.prop("scrollHeight"));
                }, 1000);
            });

        });
    </script>
    @endif
</body>
</html>
