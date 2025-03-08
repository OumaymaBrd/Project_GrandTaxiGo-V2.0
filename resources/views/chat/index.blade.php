<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat en temps réel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="container mx-auto py-8 px-4 max-w-4xl">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-4 bg-indigo-600 text-white">
                <h1 class="text-xl font-bold">Chat en temps réel</h1>
                <p class="text-sm opacity-80">Communiquez avec votre chauffeur/passager</p>
            </div>

            <div id="chat-messages" class="h-96 overflow-y-auto p-4 space-y-4">
                <!-- Les messages seront affichés ici -->
            </div>

            <div class="border-t border-gray-200 p-4">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 mr-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="nickname"
                        class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Votre nom"
                    >
                </div>

                <div class="flex items-end space-x-2">
                    <div class="flex-1">
                        <textarea
                            id="message"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Tapez votre message ici..."
                            rows="3"
                        ></textarea>
                    </div>
                    <button
                        id="send-button"
                        class="bg-indigo-600 text-white rounded-lg px-4 py-2 hover:bg-indigo-700 transition duration-200 flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                        </svg>
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>

