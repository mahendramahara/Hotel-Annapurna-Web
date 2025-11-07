<div class="reviews-container">
        <div class="reviews-title">
            <ion-icon name="chatbubbles-outline"></ion-icon>
            <span>Customer Reviews</span>
        </div>

        <div class="reviews-table-wrapper">
            <table class="reviews-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Name</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Rating</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="reviewsTableBody">
                    <!-- Table content will be dynamically populated -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Review Modal -->
    <div id="viewModal" class="reviews-modal">
        <div class="reviews-modal-content">
            <div class="reviews-modal-header">
                <img id="modalCustomerImage" src="" alt="Customer">
                <div>
                    <h3 id="modalCustomerName"></h3>
                    <p id="modalServiceInfo"></p>
                </div>
            </div>
            <div class="reviews-rating" id="modalRating"></div>
            <p id="modalReviewText"></p>
            <div class="reviews-image-grid" id="modalImages"></div>
            <div class="reviews-modal-actions">
                <button class="reviews-btn reviews-btn-secondary" onclick="closeModal('viewModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Respond Modal -->
    <div id="respondModal" class="reviews-modal">
        <div class="reviews-modal-content">
            <h3>Respond to Review</h3>
            <p id="respondCustomerInfo"></p>
            <form id="responseForm" class="reviews-response-form">
                <textarea placeholder="Write your response..." required></textarea>
                <div class="reviews-modal-actions">
                    <button type="button" class="reviews-btn reviews-btn-secondary" onclick="closeModal('respondModal')">Cancel</button>
                    <button type="submit" class="reviews-btn reviews-btn-primary">Send Response</button>
                </div>
            </form>
        </div>
    </div>