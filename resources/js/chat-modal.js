/**
 * Chat Modal for Driver-Passenger Communication
 *
 * This script handles the chat modal functionality for the ride-sharing application.
 * It allows passengers and drivers to communicate in real-time.
 */

import axios from "axios"

class ChatModal {
  constructor() {
    // Elements
    this.modal = document.getElementById("chat-modal")
    this.closeButton = document.getElementById("close-chat-modal")
    this.title = document.getElementById("chat-modal-title")
    this.recipientName = document.getElementById("chat-recipient-name")
    this.messagesContainer = document.getElementById("chat-modal-messages")
    this.messageInput = document.getElementById("chat-modal-input")
    this.sendButton = document.getElementById("chat-modal-send")

    // State
    this.currentRideId = null
    this.currentRecipient = null
    this.currentUserType = null // 'driver' or 'passenger'

    // Bind events
    this.bindEvents()
    this.setupEchoListeners()
  }

  bindEvents() {
    // Close modal
    this.closeButton.addEventListener("click", () => this.hideModal())

    // Send message
    this.sendButton.addEventListener("click", () => this.sendMessage())

    // Send on Enter (but allow Shift+Enter for new lines)
    this.messageInput.addEventListener("keydown", (e) => {
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault()
        this.sendMessage()
      }
    })

    // Listen for open chat buttons
    document.addEventListener("click", (e) => {
      if (e.target.matches("[data-open-chat]") || e.target.closest("[data-open-chat]")) {
        const button = e.target.matches("[data-open-chat]") ? e.target : e.target.closest("[data-open-chat]")

        const rideId = button.dataset.rideId
        const recipientName = button.dataset.recipientName
        const recipientId = button.dataset.recipientId
        const userType = button.dataset.userType // 'driver' or 'passenger'

        this.openChat(rideId, recipientName, recipientId, userType)
      }
    })
  }

  setupEchoListeners() {
    // Listen for new messages in the ride channel
    window.Echo.channel("ride-chat").listen(".new-message", (data) => {
      // Only show messages for the current ride
      if (data.ride_id === this.currentRideId) {
        this.addMessageToChat(data)
      }
    })
  }

  openChat(rideId, recipientName, recipientId, userType) {
    this.currentRideId = rideId
    this.currentRecipient = recipientId
    this.currentUserType = userType

    // Update modal title
    this.recipientName.textContent = recipientName

    // Clear previous messages
    this.messagesContainer.innerHTML = ""

    // Show loading indicator
    this.messagesContainer.innerHTML = `
            <div class="flex justify-center items-center h-full">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
        `

    // Load previous messages
    this.loadMessages(rideId)

    // Show modal
    this.showModal()
  }

  async loadMessages(rideId) {
    try {
      const response = await axios.get(`/api/rides/${rideId}/messages`)

      // Clear loading indicator
      this.messagesContainer.innerHTML = ""

      // Add messages to chat
      if (response.data.messages.length === 0) {
        this.messagesContainer.innerHTML = `
                    <div class="text-center text-gray-500 py-4">
                        Aucun message. Commencez la conversation!
                    </div>
                `
      } else {
        response.data.messages.forEach((message) => {
          this.addMessageToChat(message)
        })
      }

      // Scroll to bottom
      this.scrollToBottom()
    } catch (error) {
      console.error("Error loading messages:", error)
      this.messagesContainer.innerHTML = `
                <div class="text-center text-red-500 py-4">
                    Erreur lors du chargement des messages. Veuillez réessayer.
                </div>
            `
    }
  }

  async sendMessage() {
    const message = this.messageInput.value.trim()

    if (!message) return

    // Disable send button
    this.sendButton.disabled = true

    try {
      await axios.post("/api/messages", {
        ride_id: this.currentRideId,
        recipient_id: this.currentRecipient,
        recipient_type: this.currentUserType === "driver" ? "passenger" : "driver",
        message: message,
      })

      // Clear input
      this.messageInput.value = ""

      // No need to add message to chat, it will come through Echo
    } catch (error) {
      console.error("Error sending message:", error)
      alert("Erreur lors de l'envoi du message. Veuillez réessayer.")
    } finally {
      // Re-enable send button
      this.sendButton.disabled = false

      // Focus on input
      this.messageInput.focus()
    }
  }

  addMessageToChat(message) {
    const isCurrentUser = message.sender_type === this.currentUserType

    const messageElement = document.createElement("div")
    messageElement.className = isCurrentUser ? "flex justify-end" : "flex justify-start"

    messageElement.innerHTML = `
            <div class="${isCurrentUser ? "bg-indigo-600 text-white" : "bg-gray-200 text-gray-800"} rounded-lg px-4 py-2 max-w-xs sm:max-w-md">
                <div>${message.message}</div>
                <div class="text-xs opacity-70 text-right mt-1">
                    ${new Date(message.created_at).toLocaleTimeString()}
                </div>
            </div>
        `

    this.messagesContainer.appendChild(messageElement)
    this.scrollToBottom()
  }

  scrollToBottom() {
    this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight
  }

  showModal() {
    this.modal.classList.remove("hidden")
    this.messageInput.focus()
  }

  hideModal() {
    this.modal.classList.add("hidden")
    this.currentRideId = null
    this.currentRecipient = null
  }
}

// Initialize chat modal when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  window.chatModal = new ChatModal()
})

export default ChatModal

