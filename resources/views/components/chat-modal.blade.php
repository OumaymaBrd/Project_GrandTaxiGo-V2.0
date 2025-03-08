<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" id="chat-modal">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden">
        <div class="p-4 bg-indigo-600 text-white flex justify-between items-center">
            <h3 class="text-lg font-bold" id="chat-modal-title">Chat avec <span id="chat-recipient-name"></span></h3>
            <button id="close-chat-modal" class="text-white hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div id="chat-modal-messages" class="h-80 overflow-y-auto p-4 space-y-4">
            <!-- Messages will be displayed here -->
        </div>

        <div class="border-t border-gray-200 p-4">
            <div class="flex items-end space-x-2">
                <div class="flex-1">
                    <textarea
                        id="chat-modal-input"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Tapez votre message ici..."
                        rows="2"
                    ></textarea>
                </div>
                <button
                    id="chat-modal-send"
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

