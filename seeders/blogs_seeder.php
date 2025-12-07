<?php
/**
 * Blogs Seeder
 * Creates 20 detailed blog posts about Hotel Annapurna, Nepali cuisine, and hospitality
 */

require_once __DIR__ . '/../config/db.php';

function seedBlogs($conn) {
    echo "<h3>📝 Seeding Blog Posts...</h3>";
    
    // Get author IDs (admin users)
    $author_result = $conn->query("SELECT id FROM users WHERE role = 'admin' LIMIT 2");
    $authors = [];
    while ($row = $author_result->fetch_assoc()) {
        $authors[] = $row['id'];
    }
    
    if (empty($authors)) {
        echo "<div class='error'>❌ No admin users found. Please seed users first.</div>";
        return 0;
    }
    
    $blogs = [
        [
            'title' => 'The Rich Heritage of Nepali Cuisine: A Culinary Journey',
            'category' => 'Food & Culture',
            'tags' => 'nepali food, cuisine, culture, tradition',
            'content' => '<h2>Discovering the Flavors of Nepal</h2>
<p>Nepali cuisine is a beautiful blend of flavors, traditions, and cultural influences that have evolved over centuries. At Hotel Annapurna, we take pride in serving authentic Nepali dishes that tell the story of our rich heritage.</p>

<h3>The Foundation of Nepali Cooking</h3>
<p>The heart of Nepali cuisine lies in its simplicity and the use of fresh, local ingredients. Dal Bhat, the national dish, is more than just lentils and rice – it\'s a complete meal that represents the Nepali way of life. Served with tarkari (vegetable curry), achar (pickle), and sometimes meat, it provides balanced nutrition and incredible taste.</p>

<h3>Regional Diversity</h3>
<p>Nepal\'s diverse geography creates unique culinary traditions across different regions. The Newari community contributes dishes like choila and bara, while Thakali cuisine brings us the famous Thakali thali. Each region has its own specialty, making Nepali food incredibly diverse.</p>

<h3>Spices and Flavors</h3>
<p>Nepali cooking uses a variety of spices including cumin, coriander, turmeric, and timur (Sichuan pepper). These spices not only add flavor but also have medicinal properties that have been recognized in Ayurvedic traditions for centuries.</p>

<h3>Modern Interpretations</h3>
<p>At Hotel Annapurna, we respect traditional recipes while also creating modern interpretations that appeal to contemporary tastes. Our chefs carefully balance authenticity with innovation to create memorable dining experiences.</p>

<p>Join us on this culinary journey and experience the true taste of Nepal!</p>',
            'status' => 'published'
        ],
        [
            'title' => '10 Must-Try Dishes at Hotel Annapurna',
            'category' => 'Restaurant',
            'tags' => 'menu, recommendations, food, dining',
            'content' => '<h2>Our Signature Dishes You Cannot Miss</h2>
<p>With so many delicious options on our menu, choosing what to order can be overwhelming. Here\'s our guide to the top 10 must-try dishes at Hotel Annapurna.</p>

<h3>1. Newari Khaja Set</h3>
<p>This traditional Newari platter is a feast for the senses. It includes beaten rice (chiura), spicy choila, black lentil patties (bara), and various pickles. Available on weekends, it\'s perfect for sharing with friends and family.</p>

<h3>2. Thakali Thali</h3>
<p>Our Thakali Thali is a complete meal that showcases the culinary excellence of the Thakali community. It includes rice, dal, seasonal vegetables, gundruk (fermented greens), and your choice of meat curry.</p>

<h3>3. Tandoori Chicken</h3>
<p>Marinated for 24 hours in yogurt and spices, our tandoori chicken is cooked to perfection in a traditional clay oven. The result is tender, juicy meat with a smoky flavor.</p>

<h3>4. Momo (Dumplings)</h3>
<p>No visit to a Nepali restaurant is complete without trying momos. We offer both vegetable and chicken varieties, steamed to perfection and served with spicy achar.</p>

<h3>5. Chicken Chhoila</h3>
<p>Grilled chicken marinated in authentic Newari spices, served with beaten rice. This dish perfectly balances spicy, tangy, and smoky flavors.</p>

<h3>6. Dal Bhat Tarkari</h3>
<p>The soul food of Nepal. Our version includes perfectly cooked rice, hearty lentil soup, seasonal vegetables, and homemade pickle.</p>

<h3>7. Mutton Curry</h3>
<p>Slow-cooked mutton in a rich, aromatic gravy made with traditional Nepali spices. This dish is comfort in a bowl.</p>

<h3>8. Fish Fry</h3>
<p>Fresh fish marinated in herbs and spices, then fried to golden perfection. Available on selected days, it\'s crispy on the outside and tender inside.</p>

<h3>9. Paneer Butter Masala</h3>
<p>For our vegetarian guests, this creamy cottage cheese curry in tomato-butter gravy is absolutely delicious.</p>

<h3>10. Royal Annapurna Feast</h3>
<p>Can\'t decide? Try our grand feast that includes multiple courses with appetizers, mains, and desserts. It\'s a culinary journey through Nepal.</p>

<p>Visit us today and treat yourself to these amazing dishes!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'The Art of Making Perfect Momos: Behind the Scenes',
            'category' => 'Cooking Tips',
            'tags' => 'momos, cooking, recipe, kitchen',
            'content' => '<h2>Secrets of Our Famous Momos</h2>
<p>Momos are Nepal\'s beloved dumplings, and at Hotel Annapurna, we\'ve perfected the art of making them. Let us share some secrets from our kitchen.</p>

<h3>The Perfect Dough</h3>
<p>The foundation of great momos is the dough. We use all-purpose flour mixed with just the right amount of water to create a smooth, elastic dough. The key is kneading it well and letting it rest for at least 30 minutes.</p>

<h3>Filling Varieties</h3>
<p>We offer multiple filling options. Our vegetable momos are packed with cabbage, carrots, onions, and aromatic spices. The chicken momos feature minced chicken mixed with ginger, garlic, and cilantro.</p>

<h3>The Folding Technique</h3>
<p>The traditional pleating technique isn\'t just for aesthetics – it ensures the momos stay sealed during steaming while creating the perfect texture. Our experienced chefs can fold hundreds of momos perfectly in an hour!</p>

<h3>Steaming to Perfection</h3>
<p>We steam our momos in traditional bamboo steamers. This method ensures even cooking and adds a subtle flavor that metal steamers cannot replicate.</p>

<h3>The Achar (Sauce)</h3>
<p>No momo is complete without the perfect achar. Our signature tomato-based achar includes roasted sesame seeds, timur (Sichuan pepper), and fresh cilantro.</p>

<p>Next time you enjoy our momos, you\'ll appreciate the skill and tradition behind every dumpling!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Hotel Annapurna: Where Comfort Meets Luxury',
            'category' => 'Hotel',
            'tags' => 'accommodation, rooms, hotel, luxury',
            'content' => '<h2>Experience Unmatched Hospitality</h2>
<p>Hotel Annapurna offers more than just a place to stay – we provide an experience that combines traditional Nepali hospitality with modern luxury.</p>

<h3>Our Room Categories</h3>
<p>We offer four types of accommodations to suit every need and budget. Our single rooms are perfect for solo travelers, while double rooms provide comfort for couples. For those seeking more space, our deluxe rooms and suites offer premium amenities and stunning views.</p>

<h3>Modern Amenities</h3>
<p>Every room features high-speed WiFi, air conditioning, flat-screen TVs, and comfortable bedding. Our suites include full kitchens, living areas, and private balconies with panoramic views of the city or mountains.</p>

<h3>Personalized Service</h3>
<p>Our staff is trained to provide attentive service that anticipates your needs. From 24/7 room service to concierge assistance, we\'re here to make your stay memorable.</p>

<h3>Location and Accessibility</h3>
<p>Conveniently located in the heart of Kathmandu, Hotel Annapurna provides easy access to major attractions, business districts, and shopping areas.</p>

<p>Book your stay today and discover why guests choose Hotel Annapurna time and again!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Sustainable Practices in Our Kitchen',
            'category' => 'Sustainability',
            'tags' => 'sustainability, environment, green, organic',
            'content' => '<h2>Our Commitment to the Environment</h2>
<p>At Hotel Annapurna, we believe in responsible business practices that protect our environment while delivering exceptional quality to our guests.</p>

<h3>Local Sourcing</h3>
<p>We source over 80% of our ingredients from local farmers and suppliers. This not only ensures freshness but also supports local communities and reduces our carbon footprint.</p>

<h3>Organic Produce</h3>
<p>Whenever possible, we use organic vegetables and herbs. Our rooftop garden provides fresh herbs for our kitchen, including cilantro, mint, and basil.</p>

<h3>Waste Management</h3>
<p>We\'ve implemented a comprehensive waste management system that includes composting organic waste, recycling, and minimizing single-use plastics.</p>

<h3>Energy Efficiency</h3>
<p>Our kitchen uses energy-efficient appliances and we\'ve installed solar panels to reduce our dependence on conventional energy sources.</p>

<h3>Training and Awareness</h3>
<p>All our staff members receive regular training on sustainable practices, ensuring that environmental consciousness is part of our daily operations.</p>

<p>Together, we\'re working towards a greener future while serving delicious food!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'The Story Behind Our Name: Mount Annapurna',
            'category' => 'About Us',
            'tags' => 'history, annapurna, mountains, nepal',
            'content' => '<h2>Named After the Goddess of Food</h2>
<p>Hotel Annapurna takes its name from Mount Annapurna, the tenth highest peak in the world and one of Nepal\'s most magnificent mountains.</p>

<h3>The Mountain</h3>
<p>Mount Annapurna stands at 8,091 meters and is part of the Annapurna mountain range in north-central Nepal. The name comes from Sanskrit, meaning "Goddess of Food and Nourishment" (Anna = food, Purna = filled with).</p>

<h3>Symbolism</h3>
<p>We chose this name because it perfectly represents our mission: to nourish our guests with delicious food and warm hospitality, just as the goddess Annapurna is said to nourish the world.</p>

<h3>Our Heritage</h3>
<p>Since our establishment, we\'ve been committed to upholding the values that Mount Annapurna represents – strength, beauty, and abundance. Our restaurant and hotel embody these principles in everything we do.</p>

<h3>Connection to Nature</h3>
<p>Just as Mount Annapurna attracts trekkers from around the world, we aim to attract food lovers and travelers who seek authentic Nepali experiences.</p>

<p>Visit us and experience the spirit of Annapurna in every meal and every stay!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Special Dietary Options: Vegan and Gluten-Free Delights',
            'category' => 'Food & Health',
            'tags' => 'vegan, gluten-free, healthy, dietary',
            'content' => '<h2>Catering to All Dietary Needs</h2>
<p>At Hotel Annapurna, we believe everyone should enjoy delicious food, regardless of dietary restrictions or preferences.</p>

<h3>Vegan Options</h3>
<p>We offer an extensive selection of vegan dishes that don\'t compromise on flavor. From vegetable momos to dal bhat and mushroom chhoila, our vegan menu showcases the naturally plant-based richness of Nepali cuisine.</p>

<h3>Gluten-Free Choices</h3>
<p>Many traditional Nepali dishes are naturally gluten-free. We also offer rice-based alternatives and can modify dishes to accommodate gluten sensitivities.</p>

<h3>Nutritional Balance</h3>
<p>Our chefs work closely with nutritionists to ensure all our special dietary menus provide complete nutrition without sacrificing authentic flavors.</p>

<h3>Allergen Information</h3>
<p>We maintain detailed allergen information for all our dishes and our staff is trained to assist guests with specific dietary requirements.</p>

<p>Let us know your dietary needs, and we\'ll create a memorable dining experience just for you!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Celebrating Festivals at Hotel Annapurna',
            'category' => 'Events',
            'tags' => 'festivals, celebrations, events, culture',
            'content' => '<h2>Join Our Festival Celebrations</h2>
<p>Nepal is a land of festivals, and at Hotel Annapurna, we celebrate them all with special menus, decorations, and events.</p>

<h3>Dashain Special</h3>
<p>During Dashain, Nepal\'s biggest festival, we offer traditional khasi (goat meat) dishes and special sweets. Our restaurant is decorated with marigold flowers and traditional Nepali ornaments.</p>

<h3>Tihar Lights</h3>
<p>For Tihar, the festival of lights, we illuminate our hotel with thousands of oil lamps and candles, creating a magical atmosphere. Special sel roti and sweets are prepared fresh daily.</p>

<h3>Newari New Year</h3>
<p>We host a grand Newari feast during the Newari New Year, featuring traditional dishes that are rarely found in regular restaurants.</p>

<h3>International Celebrations</h3>
<p>We also celebrate international occasions like Christmas, New Year, and Valentine\'s Day with special themed menus and decorations.</p>

<h3>Private Events</h3>
<p>Our banquet facilities are perfect for hosting your own celebrations, from birthdays to weddings.</p>

<p>Check our events calendar and join us for the next celebration!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Meet Our Executive Chef: Master of Flavors',
            'category' => 'Team',
            'tags' => 'chef, team, kitchen, culinary',
            'content' => '<h2>An Interview with Chef Krishna Thapa</h2>
<p>We sat down with our Executive Chef Krishna Thapa to learn about his culinary journey and philosophy.</p>

<h3>The Beginning</h3>
<p>"I started cooking with my grandmother when I was just seven years old," Chef Krishna recalls. "She taught me that cooking is not just about feeding people, it\'s about creating happiness."</p>

<h3>Training and Experience</h3>
<p>Chef Krishna trained at Nepal\'s premier culinary institute and has worked in kitchens across Asia, from Bangkok to Singapore. He returned to Nepal with a mission to elevate traditional Nepali cuisine.</p>

<h3>Culinary Philosophy</h3>
<p>"I believe in respecting ingredients and traditional techniques while embracing innovation," he explains. "Every dish should tell a story and create an emotional connection."</p>

<h3>Favorite Dish</h3>
<p>When asked about his favorite dish to prepare, Chef Krishna smiles: "It\'s always the Thakali Thali. It\'s complex, balanced, and represents everything I love about Nepali cooking."</p>

<h3>Future Plans</h3>
<p>Chef Krishna is working on a cookbook that documents traditional Nepali recipes and plans to launch cooking classes at the hotel.</p>

<p>Experience Chef Krishna\'s mastery at Hotel Annapurna!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'The Perfect Dal Bhat: Science and Tradition Combined',
            'category' => 'Cooking Tips',
            'tags' => 'dal bhat, recipe, cooking, tradition',
            'content' => '<h2>Understanding Nepal\'s National Dish</h2>
<p>Dal Bhat is more than food in Nepal – it\'s a way of life. Let\'s explore what makes the perfect Dal Bhat.</p>

<h3>The Rice</h3>
<p>We use aged basmati rice for its superior texture and aroma. The rice should be fluffy and separate, not mushy. The key is the right water-to-rice ratio and proper cooking time.</p>

<h3>The Dal</h3>
<p>Our dal uses mixed lentils (usually masoor and moong) cooked with turmeric, cumin, and garlic. The consistency should be neither too thick nor too watery – just perfect for mixing with rice.</p>

<h3>The Tarkari</h3>
<p>The vegetable curry changes with seasons. We use whatever is freshest – spinach in winter, squash in summer. Each vegetable requires different cooking times and techniques.</p>

<h3>The Accompaniments</h3>
<p>Pickle (achar) provides the tangy element, while papad adds crunch. These aren\'t just sides – they\'re integral to the complete Dal Bhat experience.</p>

<h3>Nutritional Perfection</h3>
<p>Dal Bhat provides complete protein, complex carbohydrates, fiber, and essential vitamins. It\'s a nutritionally balanced meal that has sustained Nepalis for centuries.</p>

<p>Try our Dal Bhat and taste tradition perfected!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Wine and Dine: Perfect Pairings with Nepali Cuisine',
            'category' => 'Beverages',
            'tags' => 'wine, drinks, pairing, beverages',
            'content' => '<h2>Elevating Your Dining Experience</h2>
<p>Pairing wines with Nepali cuisine might seem challenging, but the right combinations can create magical moments.</p>

<h3>With Spicy Dishes</h3>
<p>For spicy chhoila or sekuwa, try off-dry Riesling or Gewürztraminer. The slight sweetness balances the heat while complementing the complex spices.</p>

<h3>With Rich Curries</h3>
<p>Butter chicken and mutton curry pair beautifully with medium-bodied reds like Merlot or Shiraz. The wine\'s tannins cut through the richness.</p>

<h3>With Momos</h3>
<p>Light, crisp white wines like Sauvignon Blanc or Pinot Grigio work wonderfully with momos, especially vegetable varieties.</p>

<h3>Traditional Beverages</h3>
<p>Don\'t forget traditional Nepali drinks! Chyang (rice beer) and raksi (distilled alcohol) are authentic pairings that have been enjoyed for generations.</p>

<h3>Non-Alcoholic Options</h3>
<p>Our homemade lassi, masala tea, and fresh fruit juices provide excellent non-alcoholic pairing options.</p>

<p>Ask our staff for pairing recommendations during your next visit!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Behind Every Great Meal: Our Service Philosophy',
            'category' => 'Service',
            'tags' => 'service, hospitality, team, customer care',
            'content' => '<h2>Service Excellence is Our Promise</h2>
<p>At Hotel Annapurna, we believe that great food deserves equally great service. Here\'s what sets us apart.</p>

<h3>Training and Development</h3>
<p>Every team member undergoes comprehensive training covering food knowledge, service techniques, and cultural sensitivity. We invest in continuous education to maintain high standards.</p>

<h3>Attention to Detail</h3>
<p>From remembering regular guests\' preferences to anticipating needs before they\'re expressed, our staff pays attention to every detail.</p>

<h3>Nepali Hospitality</h3>
<p>We embrace the Nepali concept of "Atithi Devo Bhava" (Guest is God). This ancient principle guides every interaction with our guests.</p>

<h3>Feedback Culture</h3>
<p>We actively seek and value guest feedback. Every comment helps us improve and serves as a learning opportunity for our team.</p>

<h3>Team Spirit</h3>
<p>Our service excellence comes from a cohesive team that works together seamlessly. From kitchen to dining room, everyone plays a crucial role.</p>

<p>Experience the difference that genuine care makes!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Exploring Kathmandu: Your Guide to the City',
            'category' => 'Travel',
            'tags' => 'kathmandu, travel, tourism, guide',
            'content' => '<h2>Discover Kathmandu with Hotel Annapurna</h2>
<p>Staying at Hotel Annapurna puts you at the center of Kathmandu\'s rich cultural heritage. Here\'s your guide to exploring the city.</p>

<h3>Must-Visit Sites</h3>
<p>Durbar Square, just 15 minutes away, showcases ancient palaces and temples. Swayambhunath (Monkey Temple) offers panoramic city views. Pashupatinath and Boudhanath are essential spiritual sites.</p>

<h3>Shopping Districts</h3>
<p>Thamel, the tourist hub, is perfect for souvenirs and trekking gear. New Road offers modern shopping experiences. Don\'t miss the traditional markets in Asan.</p>

<h3>Dining Adventures</h3>
<p>While we hope you\'ll dine with us often, Kathmandu has diverse culinary options. Try local eateries for street food and fine dining restaurants for fusion cuisine.</p>

<h3>Day Trips</h3>
<p>Bhaktapur and Patan, ancient cities with stunning architecture, make excellent day trips. Nagarkot offers sunrise views of the Himalayas.</p>

<h3>Cultural Experiences</h3>
<p>Attend traditional dance performances, visit pottery workshops, or take a cooking class to immerse yourself in Nepali culture.</p>

<p>Our concierge can help plan your Kathmandu adventure!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'The Secret Life of Spices in Nepali Cooking',
            'category' => 'Cooking Tips',
            'tags' => 'spices, ingredients, cooking, flavors',
            'content' => '<h2>Understanding the Spice Box</h2>
<p>Spices are the soul of Nepali cuisine. Let\'s explore the essential spices and how we use them at Hotel Annapurna.</p>

<h3>Turmeric (Besar)</h3>
<p>Beyond adding golden color, turmeric has anti-inflammatory properties. We use it in almost every curry and dal preparation.</p>

<h3>Cumin (Jeera)</h3>
<p>Both whole and ground cumin feature prominently in our cooking. Toasted cumin seeds add incredible depth to dishes.</p>

<h3>Coriander (Dhaniya)</h3>
<p>Fresh coriander leaves garnish most dishes, while ground coriander powder is essential in curry bases.</p>

<h3>Timur (Sichuan Pepper)</h3>
<p>This unique Himalayan spice creates a tingling sensation on the tongue. It\'s essential in authentic chhoila and achar.</p>

<h3>Cardamom (Alainchi)</h3>
<p>Both green and black cardamom are used. Green for sweets and tea, black for savory dishes and biryanis.</p>

<h3>Ginger and Garlic</h3>
<p>Fresh ginger-garlic paste forms the foundation of countless dishes. We prepare it fresh daily for maximum flavor.</p>

<p>Each spice tells a story and adds character to our dishes!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Corporate Events and Business Dining at Hotel Annapurna',
            'category' => 'Business',
            'tags' => 'corporate, events, meetings, business',
            'content' => '<h2>Your Partner for Business Success</h2>
<p>Hotel Annapurna provides the perfect setting for corporate events, business meetings, and professional gatherings.</p>

<h3>Meeting Facilities</h3>
<p>Our conference rooms are equipped with modern AV equipment, high-speed internet, and comfortable seating. Capacity ranges from intimate 10-person meetings to 100+ person conferences.</p>

<h3>Business Lunch Packages</h3>
<p>Our express lunch menu is designed for busy professionals. Quality meals served promptly without compromising taste or presentation.</p>

<h3>Catering Services</h3>
<p>We provide full-service catering for your office events, from working lunches to formal dinners. Custom menus available to suit your needs and budget.</p>

<h3>Private Dining Rooms</h3>
<p>For important client dinners or team celebrations, our private dining rooms offer exclusivity with personalized service.</p>

<h3>Corporate Packages</h3>
<p>We offer special corporate rates for regular business clients. Contact us to learn about our membership benefits.</p>

<p>Make Hotel Annapurna your business dining destination!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'From Farm to Table: Our Ingredient Journey',
            'category' => 'Sustainability',
            'tags' => 'farm, local, fresh, ingredients',
            'content' => '<h2>Tracing Our Ingredients</h2>
<p>Ever wondered where your food comes from? At Hotel Annapurna, we\'re proud of our ingredient sourcing story.</p>

<h3>Local Farmers Partnership</h3>
<p>We work directly with farmers in nearby villages. This relationship ensures we get the freshest produce while farmers receive fair prices.</p>

<h3>Seasonal Menus</h3>
<p>Our menu changes with seasons to feature the best available ingredients. Spring brings fresh greens, summer offers abundant vegetables, and winter provides hearty root vegetables.</p>

<h3>Quality Control</h3>
<p>Every ingredient is inspected upon arrival. We maintain strict quality standards to ensure only the best makes it to your plate.</p>

<h3>Dairy and Meat</h3>
<p>Our dairy products come from local cooperatives. Meat is sourced from certified suppliers who follow ethical and hygienic practices.</p>

<h3>Herbs and Spices</h3>
<p>Many herbs are grown in our rooftop garden. Spices are sourced from trusted suppliers who provide authentic, unadulterated products.</p>

<p>Taste the difference that quality ingredients make!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Weekend Brunch: A New Tradition at Hotel Annapurna',
            'category' => 'Restaurant',
            'tags' => 'brunch, weekend, dining, special',
            'content' => '<h2>Introducing Our Weekend Brunch Experience</h2>
<p>We\'re excited to announce our new weekend brunch menu, combining international favorites with Nepali specialties.</p>

<h3>Brunch Menu Highlights</h3>
<p>Choose from fluffy pancakes, eggs benedict, fresh fruit platters, traditional Nepali breakfast items, and our special brunch thali.</p>

<h3>Live Cooking Stations</h3>
<p>Watch our chefs prepare made-to-order omelets, dosas, and momos right in front of you. Interactive cooking adds excitement to your meal.</p>

<h3>Beverage Bar</h3>
<p>Unlimited coffee, tea, fresh juices, and lassi included with every brunch order. Add mimosas or cocktails for a festive touch.</p>

<h3>Family-Friendly</h3>
<p>Kids eat at discounted rates, and we have special children\'s menu items. Our spacious dining area accommodates families comfortably.</p>

<h3>Timing and Reservations</h3>
<p>Brunch is served every Saturday and Sunday from 10 AM to 2 PM. Advance reservations recommended, especially for large groups.</p>

<p>Join us this weekend for a memorable brunch experience!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'The Art of Hospitality: Lessons from Nepal',
            'category' => 'Culture',
            'tags' => 'hospitality, culture, tradition, nepal',
            'content' => '<h2>What Makes Nepali Hospitality Special</h2>
<p>Nepali hospitality is world-renowned, and it\'s deeply rooted in culture and tradition. Here\'s what we can learn from it.</p>

<h3>Warm Welcomes</h3>
<p>In Nepali culture, guests are greeted with genuine warmth. The traditional "Namaste" is more than a greeting – it\'s a gesture of respect and welcome.</p>

<h3>Generosity</h3>
<p>Nepalis believe in serving the best to guests, even if it means the host goes without. This spirit of generosity permeates every interaction at Hotel Annapurna.</p>

<h3>Personal Connection</h3>
<p>Unlike transactional service, Nepali hospitality focuses on building genuine connections. Our staff takes time to know guests personally.</p>

<h3>Attention to Comfort</h3>
<p>Ensuring guest comfort is paramount. From adjusting spice levels to remembering dietary preferences, we go the extra mile.</p>

<h3>Community Spirit</h3>
<p>Nepali hospitality extends beyond individual service to creating a sense of community. At our hotel, guests often become part of our extended family.</p>

<p>Experience authentic Nepali hospitality at Hotel Annapurna!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Desserts of Nepal: Sweet Endings to Remember',
            'category' => 'Food & Culture',
            'tags' => 'desserts, sweets, nepali food, traditional',
            'content' => '<h2>Exploring Nepal\'s Sweet Traditions</h2>
<p>Nepali desserts are diverse and delicious, reflecting influences from India, Tibet, and indigenous traditions.</p>

<h3>Sel Roti</h3>
<p>This ring-shaped rice bread is crispy outside and soft inside. Traditionally made during festivals, we now offer it year-round with our special recipe.</p>

<h3>Jeri (Jalebi)</h3>
<p>Deep-fried batter soaked in sugar syrup, jeri is a festival favorite. Our version is made fresh daily and served warm.</p>

<h3>Kheer (Rice Pudding)</h3>
<p>Creamy rice pudding flavored with cardamom and garnished with nuts. Comfort food at its finest.</p>

<h3>Sikarni</h3>
<p>Sweetened thick yogurt mixed with nuts and dried fruits. A cooling dessert perfect for hot days.</p>

<h3>Barfi</h3>
<p>Milk-based confection in various flavors. Our chef makes coconut, pistachio, and cardamom varieties.</p>

<h3>Modern Fusion</h3>
<p>We also create fusion desserts combining traditional flavors with contemporary techniques. Try our cardamom panna cotta or timur-infused chocolate mousse.</p>

<p>Save room for dessert at Hotel Annapurna!</p>',
            'status' => 'published'
        ],
        [
            'title' => 'Your Feedback Matters: How We Continuously Improve',
            'category' => 'Customer Service',
            'tags' => 'feedback, improvement, quality, service',
            'content' => '<h2>Building Excellence Through Your Input</h2>
<p>At Hotel Annapurna, we believe that continuous improvement is key to maintaining excellence. Your feedback drives our progress.</p>

<h3>Multiple Feedback Channels</h3>
<p>Share your experience through comment cards, online reviews, email, or speak directly to our managers. We read every piece of feedback.</p>

<h3>Quick Response</h3>
<p>We respond to all feedback within 24 hours. Positive or negative, every comment receives attention and thoughtful response.</p>

<h3>Actionable Changes</h3>
<p>Guest suggestions have led to menu additions, service improvements, and facility upgrades. Your voice directly shapes our evolution.</p>

<h3>Recognition and Rewards</h3>
<p>Regular guests and those who provide valuable feedback are recognized through our loyalty program with special discounts and exclusive offers.</p>

<h3>Transparency</h3>
<p>We share how we\'re addressing concerns and implementing suggestions. Quarterly reports detail improvements made based on guest feedback.</p>

<h3>Staff Training</h3>
<p>Feedback is incorporated into staff training programs, ensuring the entire team learns from guest experiences.</p>

<p>Thank you for helping us become better every day!</p>',
            'status' => 'published'
        ]
    ];
    
    $success = 0;
    $skipped = 0;
    
    foreach ($blogs as $blog) {
        // Check if blog already exists
        $check = $conn->prepare("SELECT id FROM blogs WHERE title = ?");
        $check->bind_param("s", $blog['title']);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            echo "<div class='warning'>⚠️ Blog <strong>" . substr($blog['title'], 0, 50) . "...</strong> already exists - skipped</div>";
            $skipped++;
            continue;
        }
        
        // Random author from available admins
        $author_id = $authors[array_rand($authors)];
        
        $stmt = $conn->prepare("INSERT INTO blogs (title, category, tags, content, featured_image, author_id, status, views) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $featured_image = "images/blogs/demoBlog.jpg";
        $views = rand(50, 500);
        
        $stmt->bind_param(
            "sssssisi",
            $blog['title'],
            $blog['category'],
            $blog['tags'],
            $blog['content'],
            $featured_image,
            $author_id,
            $blog['status'],
            $views
        );
        
        if ($stmt->execute()) {
            echo "<div class='success'>✅ Created blog: " . substr($blog['title'], 0, 60) . "...</div>";
            $success++;
        } else {
            echo "<div class='error'>❌ Error creating blog: " . $stmt->error . "</div>";
        }
        
        $stmt->close();
        $check->close();
    }
    
    echo "<div class='info'>📊 Blogs: $success created, $skipped skipped</div>";
    return $success;
}
