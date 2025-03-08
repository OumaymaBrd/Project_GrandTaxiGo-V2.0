// When displaying ride requests
function displayRideRequests(requests) {
    const container = document.getElementById("requests-list")

    container.innerHTML = requests
      .map(
        (request) => `
          <div class="card mb-3">
              <!-- Card content -->

              <div class="card-footer">
                  <div class="d-flex justify-content-end gap-2">
                      <!-- Other buttons -->

                      ${
                        request.status === "accepted" || request.status === "pending"
                          ? `
                      <a href="/chat/ride/${request.id}" class="btn btn-info btn-sm">
                          <i class="fas fa-comment me-2"></i>Message
                      </a>
                      `
                          : ""
                      }
                  </div>
              </div>
          </div>
      `,
      )
      .join("")
  }

