<!DOCTYPE html>
<html>

<head>
    <title>Laravel Reverb Chat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Chat Application</h1>
        <div class="flex">
            <!-- User List -->
            <div class="w-1/4 bg-white p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-2">Users</h2>
                <ul>
                    @foreach ($users as $user)
                        <li class="p-2 hover:bg-gray-200 cursor-pointer"
                            onclick="selectUser({{ $user->id }}, '{{ $user->name }}')">
                            {{ $user->name }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- Chat Area -->
            <div class="w-3/4 bg-white ml-4 p-4 rounded-lg shadow">
                <div id="chat-header" class="text-lg font-semibold mb-2">Select a user to chat</div>
                <div id="messages" class="h-96 overflow-y-auto mb-4 p-2 bg-gray-50 rounded"></div>
                <form id="message-form" class="flex">
                    <input type="hidden" id="receiver-id" name="receiver_id">
                    <input type="text" id="message-input" name="message" class="flex-1 p-2 border rounded"
                        placeholder="Type a message..." required>
                    <button type="submit" class="ml-2 bg-blue-500 text-white p-2 rounded">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };
        let currentUserId = null;
        let currentUserName = '';

        function selectUser(userId, userName) {
            currentUserId = userId;
            currentUserName = userName;
            document.getElementById('chat-header').innerText = `Chat with ${userName}`;
            document.getElementById('receiver-id').value = userId;
            document.getElementById('messages').innerHTML = '';
            fetchMessages();
        }

        function fetchMessages() {
            if (!currentUserId) return;
            fetch(`/chat/messages?user_id=${currentUserId}`, {
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(messages => {
                    const messagesDiv = document.getElementById('messages');
                    messagesDiv.innerHTML = '';
                    messages.forEach(msg => {
                        const isCurrentUser = msg.sender_id == {{ Auth::id() }};
                        const messageClass = isCurrentUser ? 'bg-blue-100 ml-auto' : 'bg-gray-100';
                        messagesDiv.innerHTML += `
                            <div class="p-2 m-1 rounded ${messageClass} max-w-xs">
                                <strong>${isCurrentUser ? 'You' : msg.sender.name}:</strong> ${msg.message}
                                <div class="text-xs text-gray-500">${msg.created_at}</div>
                            </div>`;
                    });
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                });
        }

        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('/chat/send', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('message-input').value = '';
                    fetchMessages();
                });
        });



        window.onload = function() {
            window.Echo.private(`chat.{{ Auth::id() }}`)
                .listen('MessageSent', (e) => {
                    if (e.message.sender_id == currentUserId || e.message.receiver_id == currentUserId) {
                        fetchMessages();
                    }
                });
        };
        setInterval(fetchMessages, 1000);
    </script>
</body>

</html>
