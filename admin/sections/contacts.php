<div class="contacts-data-table">
            <div class="title">
                <ion-icon name="mail-unread-outline" class="title-icon"></ion-icon>
                <span class="text">Customer Contact Requests</span>
            </div>
            
            <div class="contacts-container">
                <table class="contacts-table">
                    <thead>
                        <tr>
                            <th>SN (#)</th>
                            <th>Name</th>
                            <th>Email ID</th>
                            <th>Subject</th>
                            <th>Sent Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="contacts-table-body">
                        <!-- Dynamic rows will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- View More Modal -->
        <div id="contacts-view-modal" class="contacts-modal">
            <div class="contacts-modal-content">
                <span class="contacts-close-btn">&times;</span>
                <h2>Request Details</h2>
                <div id="contacts-view-details">
                    <p><strong>Name:</strong> <span id="view-name"></span></p>
                    <p><strong>Email:</strong> <span id="view-email"></span></p>
                    <p><strong>Subject:</strong> <span id="view-subject"></span></p>
                    <p><strong>Message:</strong> <span id="view-message"></span></p>
                    <p><strong>Sent Date:</strong> <span id="view-date"></span></p>
                </div>
            </div>
        </div>

        <!-- Edit Status Modal -->
        <div id="contacts-edit-modal" class="contacts-modal">
            <div class="contacts-modal-content">
                <span class="contacts-close-btn">&times;</span>
                <h2>Update Request Status</h2>
                <form id="contacts-edit-form">
                    <input type="hidden" id="edit-request-id">
                    <div class="form-group">
                        <label for="edit-status">Status:</label>
                        <select id="edit-status" required>
                            <option value="Responded">Responded</option>
                            <option value="Not Necessary">Not Necessary</option>
                            <option value="Important">Important</option>
                            <option value="Incomplete">Incomplete</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save">Save</button>
                        <button type="button" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Respond Modal -->
        <div id="contacts-respond-modal" class="contacts-modal">
            <div class="contacts-modal-content">
                <span class="contacts-close-btn">&times;</span>
                <h2>Respond to Request</h2>
                <form id="contacts-respond-form">
                    <input type="hidden" id="respond-request-id">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" id="respond-name" readonly>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" id="respond-email" readonly>
                    </div>
                    <div class="form-group">
                        <label>Subject:</label>
                        <input type="text" id="respond-subject" readonly>
                    </div>
                    <div class="form-group">
                        <label for="respond-message">Response:</label>
                        <textarea id="respond-message" required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-send">Send Response</button>
                        <button type="button" class="btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>