{{-- {{ $item }} --}}
<x-app-layout>
    <script>
        @if(session('success'))
            const roomId = "{{ session('room_id') }}";
            const isGroup = "{{ session('group') }}";
            // You can now use these in JS
            console.log("Room created:", roomId, "Group:", isGroup);
        @endif
    </script>
    {{-- <div class="container mx-auto mt-5">
        <div class="pt-5">
            @foreach ($data as $item)
                <div class="flex mb-4 justify-between py-4 px-5 rounded-2xl shadow-md ">
                    <span>
                        {{ $item->name }}
                    </span>
                    <a href="{{ route('open.chat', $item->id) }}" class=" text-blue-500 underline">chat</a>
                </div>
            @endforeach
        </div>
        
    </div> --}}

    <style>
        nav{
            display: none !important;
        }
    </style>

    
    <div class="flex h-screen">
        <!-- Sidebar (Chat List) -->
        <div class="w-1/4 bg-white border-r shadow-md flex flex-col relative">
            <section class="p-4 border-b flex justify-between items-center">
                {{-- <h2 class="text-xl font-semibold">EHM Portal</h2> --}}
                <a href="{{ route('dashboard') }}" class=" inline-block">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                </a>
                <h4>{{ Auth::user()->name }}</h4>
            </section>
            <div class="overflow-y-auto flex-1">
                <!-- Example Chat List -->
                @foreach ($data as $item)
                    <button class="w-full text-left p-4 hover:bg-gray-100 border-b flex items-center space-x-3" onclick="openChat({{ $item->id }})">
                        <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                        <div>
                            <h3 class="font-medium username-{{$item->id}}">{{ $item->name }}</h3>
                        </div>
                    </button>  
                @endforeach

                @foreach ($groups as $item)
                    <button class="w-full text-left p-4 hover:bg-gray-100 border-b flex items-center space-x-3" onclick="openChat({{ $item->id }})">
                        <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                        <div>
                            <h3 class="font-medium group group-{{$item->id}}">{{ $item->group_name }}</h3>
                        </div>
                    </button>
                @endforeach
                
                {{-- <button class="w-full text-left p-4 hover:bg-gray-100 border-b flex items-center space-x-3" onclick="openChat('Jane Smith')">
                    <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                    <div>
                        <h3 class="font-medium">Jane Smith</h3>
                        <p class="text-sm text-gray-500">Let’s meet tomorrow.</p>
                    </div>
                </button> --}}
            </div>
            <div class=" absolute bottom-2 right-3">
                <button type="button" data-modal-target="crypto-modal" data-modal-toggle="crypto-modal" class=" bg-purple-700 text-white font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                    Create Team
                </button>
            </div>

        </div>

        <!-- Chat Window -->
        <div class="flex-1 flex flex-col">
            <div id="chatHeader" class="p-4 border-b bg-white shadow-sm flex items-center space-x-3">
                <div class="w-10 h-10 bg-gray-300 rounded-full"></div>
                <h2 class="text-lg font-semibold">Select a chat</h2>
            </div>

            <!-- Chat Messages -->
            <div id="chatMessages" class="flex-1 p-4 overflow-y-auto bg-gray-50">
                <p class="text-gray-400 text-center mt-10">No chat selected</p>
            </div>

            <!-- Chat Input -->
            <div class="p-4 bg-white border-t flex items-center">
                <input id="message" type="text" placeholder="Type a message..." 
                       class="flex-1 border rounded-full px-4 py-2 focus:outline-none focus:ring focus:border-blue-300" disabled>
                <input type="hidden" name="room_id" id="room_id" value="" >
                <input type="hidden" name="receiver_id" id="receiver_id" value="" >
                <button id="sendBtn" class="ml-3 bg-blue-500 text-white px-4 py-2 rounded-full disabled:opacity-50" disabled>Send</button>
            </div>
        </div>
    </div>

    <x-group-modal :data="$data" ></x-group-modal>

    <script>

        async function openChat(id) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let username = document.querySelector(`.username-${id}`);
            let groupname = document.querySelector(`.group-${id}`);
            let chat_type = 'chat';
            if(groupname){
                chat_type = 'group';
            }else{
                document.getElementById('receiver_id').value = id;
            }
            
            let response = await fetch(`chat`,{
                method : 'POST',
                 headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ id: id, chat_type: chat_type })
            });
            let jData = await response.json();
            // console.log(jData);

            document.querySelector('#chatHeader h2').innerText = username?.innerText ?? groupname?.innerText;
            document.getElementById('room_id').value = jData.room_id;
            let html = '';
            if(Array.isArray(jData?.message?.chat)){
                jData?.message?.chat.forEach(element => {
                    if(element.sender != {{Auth::user()->id}}){
                        html += `
                        <div class="flex justify-start mb-3">
                            <div class="bg-gray-200 px-4 py-2 rounded-2xl max-w-xs">${element.message}</div>
                        </div>`;
                    }else{
                        html += `
                        <div class="flex justify-end mb-3">
                            <div class="bg-blue-500 text-white px-4 py-2 rounded-2xl max-w-xs">${element.message}</div>
                        </div>
                        `;
                    }
                });
            }

            if(jData?.success){
                // console.log('1');
                if (window.currentChannel) {
                    // if user opens another chat, leave the old channel
                    window.Echo.leave(`chat.${window.currentChannel}`);
                }
                let chatBox = document.getElementById('chatMessages');
                window.currentChannel = jData.room_id;
                window.Echo.private(`chat.${jData.room_id}`)
                    .listen('.message.sent', (e) => {
                        let newMsg = document.createElement('div');
                        let class_style = 'justify-start';
                        let class_style2 = 'bg-gray-200';
                        if(e.chat.sender == {{ Auth::user()->id }}){
                            class_style = 'justify-end';
                            class_style2 = 'bg-blue-500 text-white ';
                        }
                        newMsg.innerHTML = `
                                <div class="flex ${class_style} mb-3">
                                    <div class="${class_style2} px-4 py-2 rounded-2xl max-w-xs">${e.chat.message}</div>
                                </div>`;
                        chatBox.appendChild(newMsg);
                        setTimeout(() => {
                            chatBox.scrollTop = chatBox.scrollHeight;
                        }, 100);
                });
                setTimeout(() => {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }, 100);
            }

            document.querySelector('#chatMessages').innerHTML = html;
            document.querySelector('#message').disabled = false;
            document.querySelector('#sendBtn').disabled = false;
        }


   

            document.getElementById('sendBtn')?.addEventListener('click', async function() {
                let message = document.getElementById('message').value.trim();
                let room_id = document.getElementById('room_id').value;
                let receiver_id = document.getElementById('receiver_id').value;
                let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                if (!message) return alert('Please type a message.');

                let send_data = {
                    message, room_id
                };
                if (receiver_id) {
                    send_data.receiver_id = receiver_id;
                }

                try {
                    let response = await fetch('{{ route("send.chat") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(send_data)
                    });

                    let data = await response.json();

                    if (data.status === 'success') {
                        let chatBox = document.getElementById('chatMessages');
                        let newMsg = document.createElement('div');
                        // newMsg.innerHTML = `
                        //         <div class="flex justify-end mb-3">
                        //             <div class="bg-blue-500 text-white px-4 py-2 rounded-2xl max-w-xs">${data.data.message}</div>
                        //         </div>`;
                        // chatBox.appendChild(newMsg);

                        // ✅ Clear the input box
                        document.getElementById('message').value = '';
                        // ✅ Append new message to chat box
                    } else {
                        alert('Failed to send message.');
                    }
                } catch (error) {
                    console.error('Error sending chat:', error);
                }
            });
    </script>

</x-app-layout>