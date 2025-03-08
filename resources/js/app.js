import "./bootstrap"
import axios from "axios"

// Set up CSRF token for all axios requests
axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector('meta[name="csrf-token"]').content

document.addEventListener("DOMContentLoaded", () => {
  const nicknameInput = document.getElementById("nickname")
  const messageInput = document.getElementById("message")
  const sendButton = document.getElementById("send-button")
  const chatMessages = document.getElementById("chat-messages")

  // Function to send a message
  function sendMessage() {
    const nickname = nicknameInput.value.trim()
    const message = messageInput.value.trim()

    if (!nickname || !message) {
      alert("Veuillez entrer votre nom et un message")
      return
    }

    // Disable button during request
    sendButton.disabled = true

    axios
      .post("/chat", {
        nickname: nickname,
        message: message,
      })
      .then((response) => {
        // Clear message input on success
        messageInput.value = ""
      })
      .catch((error) => {
        console.error("Erreur lors de l'envoi du message:", error)
        alert("Erreur lors de l'envoi du message")
      })
      .finally(() => {
        // Re-enable button
        sendButton.disabled = false
        // Focus on message input
        messageInput.focus()
      })
  }

  // Send message on button click
  sendButton.addEventListener("click", sendMessage)

  // Send message on Enter key (but allow Shift+Enter for new lines)
  messageInput.addEventListener("keydown", (e) => {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault()
      sendMessage()
    }
  })

  // Listen for chat messages
  window.Echo.channel("chat").listen(".chat-message", (data) => {
    // Create message element
    const messageElement = document.createElement("div")

    // Check if this is the current user's message
    const isCurrentUser = data.nickname === nicknameInput.value.trim()

    // Apply appropriate styling based on sender
    messageElement.className = isCurrentUser ? "flex justify-end" : "flex justify-start"

    // Create message content
    messageElement.innerHTML = `
                <div class="${isCurrentUser ? "bg-indigo-600 text-white" : "bg-gray-200 text-gray-800"} rounded-lg px-4 py-2 max-w-xs sm:max-w-md">
                    ${!isCurrentUser ? `<div class="font-bold text-sm">${data.nickname}</div>` : ""}
                    <div>${data.message}</div>
                    <div class="text-xs opacity-70 text-right mt-1">
                        ${new Date().toLocaleTimeString()}
                    </div>
                </div>
            `

    // Add to chat container
    chatMessages.appendChild(messageElement)

    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight
  })
})

