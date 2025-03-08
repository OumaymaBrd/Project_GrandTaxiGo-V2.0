/**
 * Fonction améliorée pour marquer une course comme terminée
 * avec débogage détaillé
 */
window.completeRide = async (id) => {
    if (!confirm("Êtes-vous sûr de vouloir marquer cette course comme terminée ?")) {
      return
    }

    try {
      // Numéro de téléphone fixe pour les tests
      const phoneNumber = "0701237397"

      // Afficher un indicateur de chargement
      const button = document.querySelector(`button[onclick="completeRide(${id})"]`)
      if (button) {
        const originalButtonText = button.innerHTML // Declare originalButtonText
        button.disabled = true
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement en cours...'
      }

      console.log("Envoi de la requête pour compléter la course:", {
        id: id,
        phoneNumber: phoneNumber,
        url: `/api/complete-ride/${id}`,
      })

      // Utiliser une nouvelle route API dédiée
      const response = await fetch(`/api/complete-ride/${id}`, {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify({
          phone_number: phoneNumber,
        }),
      })

      console.log("Statut de la réponse:", response.status)

      // Restaurer le bouton
      if (button) {
        button.disabled = false
        button.innerHTML = originalButtonText
      }

      // Gérer la réponse
      const responseData = await response.json()
      console.log("Données de réponse:", responseData)

      if (!response.ok) {
        throw new Error(responseData.message || "Erreur lors de la mise à jour")
      }

      // Afficher le message de succès
      showAlert("success", "Course marquée comme terminée et SMS envoyé")

      // Recharger les réservations après un court délai
      setTimeout(() => {
        window.location.reload() // Recharger la page complètement
      }, 1500)
    } catch (error) {
      console.error("Erreur complète:", error)
      showAlert("error", error.message || "Erreur lors de la mise à jour de la course")
    }
  }

  /**
   * Fonction pour afficher des alertes
   */
  function showAlert(type, message) {
    const alertClass = type === "error" ? "alert-danger" : "alert-success"
    const icon = type === "error" ? "exclamation-circle" : "check-circle"

    // Créer l'alerte
    const alert = document.createElement("div")
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-4`
    alert.style.zIndex = "9999"
    alert.innerHTML = `
          <i class="fas fa-${icon} me-2"></i>
          ${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `

    // Ajouter l'alerte au document
    document.body.appendChild(alert)

    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
      alert.classList.remove("show")
      setTimeout(() => alert.remove(), 150)
    }, 5000)
  }

  /**
   * Fonction pour tester l'envoi de SMS directement depuis le dashboard
   */
  window.testSmsFromDashboard = async () => {
    try {
      const phoneNumber = "0701237397"

      console.log("Test d'envoi de SMS depuis le dashboard:", {
        phoneNumber: phoneNumber,
      })

      const response = await fetch("/api/test-sms", {
        method: "POST",
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify({
          phone_number: phoneNumber,
        }),
      })

      console.log("Statut de la réponse du test SMS:", response.status)

      const responseData = await response.json()
      console.log("Données de réponse du test SMS:", responseData)

      if (!response.ok) {
        throw new Error(responseData.message || "Erreur lors du test SMS")
      }

      showAlert("success", "Test SMS envoyé avec succès. Vérifiez votre téléphone.")
    } catch (error) {
      console.error("Erreur lors du test SMS:", error)
      showAlert("error", error.message || "Erreur lors du test SMS")
    }
  }

