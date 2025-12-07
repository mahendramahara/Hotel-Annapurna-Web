-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2025 at 09:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_annapurna_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_type` enum('order','booking','reservation','login','logout','registration','update','delete','other') NOT NULL,
  `description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `content` text NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `author_id` int(11) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `title`, `category`, `tags`, `content`, `featured_image`, `views`, `author_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 'The Rich Heritage of Nepali Cuisine: A Culinary Journey', 'Food & Culture', 'nepali food, cuisine, culture, tradition', '<h2>Discovering the Flavors of Nepal</h2>\r\n<p>Nepali cuisine is a beautiful blend of flavors, traditions, and cultural influences that have evolved over centuries. At Hotel Annapurna, we take pride in serving authentic Nepali dishes that tell the story of our rich heritage.</p>\r\n\r\n<h3>The Foundation of Nepali Cooking</h3>\r\n<p>The heart of Nepali cuisine lies in its simplicity and the use of fresh, local ingredients. Dal Bhat, the national dish, is more than just lentils and rice – it\'s a complete meal that represents the Nepali way of life. Served with tarkari (vegetable curry), achar (pickle), and sometimes meat, it provides balanced nutrition and incredible taste.</p>\r\n\r\n<h3>Regional Diversity</h3>\r\n<p>Nepal\'s diverse geography creates unique culinary traditions across different regions. The Newari community contributes dishes like choila and bara, while Thakali cuisine brings us the famous Thakali thali. Each region has its own specialty, making Nepali food incredibly diverse.</p>\r\n\r\n<h3>Spices and Flavors</h3>\r\n<p>Nepali cooking uses a variety of spices including cumin, coriander, turmeric, and timur (Sichuan pepper). These spices not only add flavor but also have medicinal properties that have been recognized in Ayurvedic traditions for centuries.</p>\r\n\r\n<h3>Modern Interpretations</h3>\r\n<p>At Hotel Annapurna, we respect traditional recipes while also creating modern interpretations that appeal to contemporary tastes. Our chefs carefully balance authenticity with innovation to create memorable dining experiences.</p>\r\n\r\n<p>Join us on this culinary journey and experience the true taste of Nepal!</p>', 'images/blogs/demoBlog.jpg', 498, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(2, '10 Must-Try Dishes at Hotel Annapurna', 'Restaurant', 'menu, recommendations, food, dining', '<h2>Our Signature Dishes You Cannot Miss</h2>\r\n<p>With so many delicious options on our menu, choosing what to order can be overwhelming. Here\'s our guide to the top 10 must-try dishes at Hotel Annapurna.</p>\r\n\r\n<h3>1. Newari Khaja Set</h3>\r\n<p>This traditional Newari platter is a feast for the senses. It includes beaten rice (chiura), spicy choila, black lentil patties (bara), and various pickles. Available on weekends, it\'s perfect for sharing with friends and family.</p>\r\n\r\n<h3>2. Thakali Thali</h3>\r\n<p>Our Thakali Thali is a complete meal that showcases the culinary excellence of the Thakali community. It includes rice, dal, seasonal vegetables, gundruk (fermented greens), and your choice of meat curry.</p>\r\n\r\n<h3>3. Tandoori Chicken</h3>\r\n<p>Marinated for 24 hours in yogurt and spices, our tandoori chicken is cooked to perfection in a traditional clay oven. The result is tender, juicy meat with a smoky flavor.</p>\r\n\r\n<h3>4. Momo (Dumplings)</h3>\r\n<p>No visit to a Nepali restaurant is complete without trying momos. We offer both vegetable and chicken varieties, steamed to perfection and served with spicy achar.</p>\r\n\r\n<h3>5. Chicken Chhoila</h3>\r\n<p>Grilled chicken marinated in authentic Newari spices, served with beaten rice. This dish perfectly balances spicy, tangy, and smoky flavors.</p>\r\n\r\n<h3>6. Dal Bhat Tarkari</h3>\r\n<p>The soul food of Nepal. Our version includes perfectly cooked rice, hearty lentil soup, seasonal vegetables, and homemade pickle.</p>\r\n\r\n<h3>7. Mutton Curry</h3>\r\n<p>Slow-cooked mutton in a rich, aromatic gravy made with traditional Nepali spices. This dish is comfort in a bowl.</p>\r\n\r\n<h3>8. Fish Fry</h3>\r\n<p>Fresh fish marinated in herbs and spices, then fried to golden perfection. Available on selected days, it\'s crispy on the outside and tender inside.</p>\r\n\r\n<h3>9. Paneer Butter Masala</h3>\r\n<p>For our vegetarian guests, this creamy cottage cheese curry in tomato-butter gravy is absolutely delicious.</p>\r\n\r\n<h3>10. Royal Annapurna Feast</h3>\r\n<p>Can\'t decide? Try our grand feast that includes multiple courses with appetizers, mains, and desserts. It\'s a culinary journey through Nepal.</p>\r\n\r\n<p>Visit us today and treat yourself to these amazing dishes!</p>', 'images/blogs/demoBlog.jpg', 155, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(3, 'The Art of Making Perfect Momos: Behind the Scenes', 'Cooking Tips', 'momos, cooking, recipe, kitchen', '<h2>Secrets of Our Famous Momos</h2>\r\n<p>Momos are Nepal\'s beloved dumplings, and at Hotel Annapurna, we\'ve perfected the art of making them. Let us share some secrets from our kitchen.</p>\r\n\r\n<h3>The Perfect Dough</h3>\r\n<p>The foundation of great momos is the dough. We use all-purpose flour mixed with just the right amount of water to create a smooth, elastic dough. The key is kneading it well and letting it rest for at least 30 minutes.</p>\r\n\r\n<h3>Filling Varieties</h3>\r\n<p>We offer multiple filling options. Our vegetable momos are packed with cabbage, carrots, onions, and aromatic spices. The chicken momos feature minced chicken mixed with ginger, garlic, and cilantro.</p>\r\n\r\n<h3>The Folding Technique</h3>\r\n<p>The traditional pleating technique isn\'t just for aesthetics – it ensures the momos stay sealed during steaming while creating the perfect texture. Our experienced chefs can fold hundreds of momos perfectly in an hour!</p>\r\n\r\n<h3>Steaming to Perfection</h3>\r\n<p>We steam our momos in traditional bamboo steamers. This method ensures even cooking and adds a subtle flavor that metal steamers cannot replicate.</p>\r\n\r\n<h3>The Achar (Sauce)</h3>\r\n<p>No momo is complete without the perfect achar. Our signature tomato-based achar includes roasted sesame seeds, timur (Sichuan pepper), and fresh cilantro.</p>\r\n\r\n<p>Next time you enjoy our momos, you\'ll appreciate the skill and tradition behind every dumpling!</p>', 'images/blogs/demoBlog.jpg', 265, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(4, 'Hotel Annapurna: Where Comfort Meets Luxury', 'Hotel', 'accommodation, rooms, hotel, luxury', '<h2>Experience Unmatched Hospitality</h2>\r\n<p>Hotel Annapurna offers more than just a place to stay – we provide an experience that combines traditional Nepali hospitality with modern luxury.</p>\r\n\r\n<h3>Our Room Categories</h3>\r\n<p>We offer four types of accommodations to suit every need and budget. Our single rooms are perfect for solo travelers, while double rooms provide comfort for couples. For those seeking more space, our deluxe rooms and suites offer premium amenities and stunning views.</p>\r\n\r\n<h3>Modern Amenities</h3>\r\n<p>Every room features high-speed WiFi, air conditioning, flat-screen TVs, and comfortable bedding. Our suites include full kitchens, living areas, and private balconies with panoramic views of the city or mountains.</p>\r\n\r\n<h3>Personalized Service</h3>\r\n<p>Our staff is trained to provide attentive service that anticipates your needs. From 24/7 room service to concierge assistance, we\'re here to make your stay memorable.</p>\r\n\r\n<h3>Location and Accessibility</h3>\r\n<p>Conveniently located in the heart of Kathmandu, Hotel Annapurna provides easy access to major attractions, business districts, and shopping areas.</p>\r\n\r\n<p>Book your stay today and discover why guests choose Hotel Annapurna time and again!</p>', 'images/blogs/demoBlog.jpg', 198, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(5, 'Sustainable Practices in Our Kitchen', 'Sustainability', 'sustainability, environment, green, organic', '<h2>Our Commitment to the Environment</h2>\r\n<p>At Hotel Annapurna, we believe in responsible business practices that protect our environment while delivering exceptional quality to our guests.</p>\r\n\r\n<h3>Local Sourcing</h3>\r\n<p>We source over 80% of our ingredients from local farmers and suppliers. This not only ensures freshness but also supports local communities and reduces our carbon footprint.</p>\r\n\r\n<h3>Organic Produce</h3>\r\n<p>Whenever possible, we use organic vegetables and herbs. Our rooftop garden provides fresh herbs for our kitchen, including cilantro, mint, and basil.</p>\r\n\r\n<h3>Waste Management</h3>\r\n<p>We\'ve implemented a comprehensive waste management system that includes composting organic waste, recycling, and minimizing single-use plastics.</p>\r\n\r\n<h3>Energy Efficiency</h3>\r\n<p>Our kitchen uses energy-efficient appliances and we\'ve installed solar panels to reduce our dependence on conventional energy sources.</p>\r\n\r\n<h3>Training and Awareness</h3>\r\n<p>All our staff members receive regular training on sustainable practices, ensuring that environmental consciousness is part of our daily operations.</p>\r\n\r\n<p>Together, we\'re working towards a greener future while serving delicious food!</p>', 'images/blogs/demoBlog.jpg', 136, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(6, 'The Story Behind Our Name: Mount Annapurna', 'About Us', 'history, annapurna, mountains, nepal', '<h2>Named After the Goddess of Food</h2>\r\n<p>Hotel Annapurna takes its name from Mount Annapurna, the tenth highest peak in the world and one of Nepal\'s most magnificent mountains.</p>\r\n\r\n<h3>The Mountain</h3>\r\n<p>Mount Annapurna stands at 8,091 meters and is part of the Annapurna mountain range in north-central Nepal. The name comes from Sanskrit, meaning \"Goddess of Food and Nourishment\" (Anna = food, Purna = filled with).</p>\r\n\r\n<h3>Symbolism</h3>\r\n<p>We chose this name because it perfectly represents our mission: to nourish our guests with delicious food and warm hospitality, just as the goddess Annapurna is said to nourish the world.</p>\r\n\r\n<h3>Our Heritage</h3>\r\n<p>Since our establishment, we\'ve been committed to upholding the values that Mount Annapurna represents – strength, beauty, and abundance. Our restaurant and hotel embody these principles in everything we do.</p>\r\n\r\n<h3>Connection to Nature</h3>\r\n<p>Just as Mount Annapurna attracts trekkers from around the world, we aim to attract food lovers and travelers who seek authentic Nepali experiences.</p>\r\n\r\n<p>Visit us and experience the spirit of Annapurna in every meal and every stay!</p>', 'images/blogs/demoBlog.jpg', 85, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(7, 'Special Dietary Options: Vegan and Gluten-Free Delights', 'Food & Health', 'vegan, gluten-free, healthy, dietary', '<h2>Catering to All Dietary Needs</h2>\r\n<p>At Hotel Annapurna, we believe everyone should enjoy delicious food, regardless of dietary restrictions or preferences.</p>\r\n\r\n<h3>Vegan Options</h3>\r\n<p>We offer an extensive selection of vegan dishes that don\'t compromise on flavor. From vegetable momos to dal bhat and mushroom chhoila, our vegan menu showcases the naturally plant-based richness of Nepali cuisine.</p>\r\n\r\n<h3>Gluten-Free Choices</h3>\r\n<p>Many traditional Nepali dishes are naturally gluten-free. We also offer rice-based alternatives and can modify dishes to accommodate gluten sensitivities.</p>\r\n\r\n<h3>Nutritional Balance</h3>\r\n<p>Our chefs work closely with nutritionists to ensure all our special dietary menus provide complete nutrition without sacrificing authentic flavors.</p>\r\n\r\n<h3>Allergen Information</h3>\r\n<p>We maintain detailed allergen information for all our dishes and our staff is trained to assist guests with specific dietary requirements.</p>\r\n\r\n<p>Let us know your dietary needs, and we\'ll create a memorable dining experience just for you!</p>', 'images/blogs/demoBlog.jpg', 88, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(8, 'Celebrating Festivals at Hotel Annapurna', 'Events', 'festivals, celebrations, events, culture', '<h2>Join Our Festival Celebrations</h2>\r\n<p>Nepal is a land of festivals, and at Hotel Annapurna, we celebrate them all with special menus, decorations, and events.</p>\r\n\r\n<h3>Dashain Special</h3>\r\n<p>During Dashain, Nepal\'s biggest festival, we offer traditional khasi (goat meat) dishes and special sweets. Our restaurant is decorated with marigold flowers and traditional Nepali ornaments.</p>\r\n\r\n<h3>Tihar Lights</h3>\r\n<p>For Tihar, the festival of lights, we illuminate our hotel with thousands of oil lamps and candles, creating a magical atmosphere. Special sel roti and sweets are prepared fresh daily.</p>\r\n\r\n<h3>Newari New Year</h3>\r\n<p>We host a grand Newari feast during the Newari New Year, featuring traditional dishes that are rarely found in regular restaurants.</p>\r\n\r\n<h3>International Celebrations</h3>\r\n<p>We also celebrate international occasions like Christmas, New Year, and Valentine\'s Day with special themed menus and decorations.</p>\r\n\r\n<h3>Private Events</h3>\r\n<p>Our banquet facilities are perfect for hosting your own celebrations, from birthdays to weddings.</p>\r\n\r\n<p>Check our events calendar and join us for the next celebration!</p>', 'images/blogs/demoBlog.jpg', 479, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(9, 'Meet Our Executive Chef: Master of Flavors', 'Team', 'chef, team, kitchen, culinary', '<h2>An Interview with Chef Krishna Thapa</h2>\r\n<p>We sat down with our Executive Chef Krishna Thapa to learn about his culinary journey and philosophy.</p>\r\n\r\n<h3>The Beginning</h3>\r\n<p>\"I started cooking with my grandmother when I was just seven years old,\" Chef Krishna recalls. \"She taught me that cooking is not just about feeding people, it\'s about creating happiness.\"</p>\r\n\r\n<h3>Training and Experience</h3>\r\n<p>Chef Krishna trained at Nepal\'s premier culinary institute and has worked in kitchens across Asia, from Bangkok to Singapore. He returned to Nepal with a mission to elevate traditional Nepali cuisine.</p>\r\n\r\n<h3>Culinary Philosophy</h3>\r\n<p>\"I believe in respecting ingredients and traditional techniques while embracing innovation,\" he explains. \"Every dish should tell a story and create an emotional connection.\"</p>\r\n\r\n<h3>Favorite Dish</h3>\r\n<p>When asked about his favorite dish to prepare, Chef Krishna smiles: \"It\'s always the Thakali Thali. It\'s complex, balanced, and represents everything I love about Nepali cooking.\"</p>\r\n\r\n<h3>Future Plans</h3>\r\n<p>Chef Krishna is working on a cookbook that documents traditional Nepali recipes and plans to launch cooking classes at the hotel.</p>\r\n\r\n<p>Experience Chef Krishna\'s mastery at Hotel Annapurna!</p>', 'images/blogs/demoBlog.jpg', 403, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(10, 'The Perfect Dal Bhat: Science and Tradition Combined', 'Cooking Tips', 'dal bhat, recipe, cooking, tradition', '<h2>Understanding Nepal\'s National Dish</h2>\r\n<p>Dal Bhat is more than food in Nepal – it\'s a way of life. Let\'s explore what makes the perfect Dal Bhat.</p>\r\n\r\n<h3>The Rice</h3>\r\n<p>We use aged basmati rice for its superior texture and aroma. The rice should be fluffy and separate, not mushy. The key is the right water-to-rice ratio and proper cooking time.</p>\r\n\r\n<h3>The Dal</h3>\r\n<p>Our dal uses mixed lentils (usually masoor and moong) cooked with turmeric, cumin, and garlic. The consistency should be neither too thick nor too watery – just perfect for mixing with rice.</p>\r\n\r\n<h3>The Tarkari</h3>\r\n<p>The vegetable curry changes with seasons. We use whatever is freshest – spinach in winter, squash in summer. Each vegetable requires different cooking times and techniques.</p>\r\n\r\n<h3>The Accompaniments</h3>\r\n<p>Pickle (achar) provides the tangy element, while papad adds crunch. These aren\'t just sides – they\'re integral to the complete Dal Bhat experience.</p>\r\n\r\n<h3>Nutritional Perfection</h3>\r\n<p>Dal Bhat provides complete protein, complex carbohydrates, fiber, and essential vitamins. It\'s a nutritionally balanced meal that has sustained Nepalis for centuries.</p>\r\n\r\n<p>Try our Dal Bhat and taste tradition perfected!</p>', 'images/blogs/demoBlog.jpg', 244, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(11, 'Wine and Dine: Perfect Pairings with Nepali Cuisine', 'Beverages', 'wine, drinks, pairing, beverages', '<h2>Elevating Your Dining Experience</h2>\r\n<p>Pairing wines with Nepali cuisine might seem challenging, but the right combinations can create magical moments.</p>\r\n\r\n<h3>With Spicy Dishes</h3>\r\n<p>For spicy chhoila or sekuwa, try off-dry Riesling or Gewürztraminer. The slight sweetness balances the heat while complementing the complex spices.</p>\r\n\r\n<h3>With Rich Curries</h3>\r\n<p>Butter chicken and mutton curry pair beautifully with medium-bodied reds like Merlot or Shiraz. The wine\'s tannins cut through the richness.</p>\r\n\r\n<h3>With Momos</h3>\r\n<p>Light, crisp white wines like Sauvignon Blanc or Pinot Grigio work wonderfully with momos, especially vegetable varieties.</p>\r\n\r\n<h3>Traditional Beverages</h3>\r\n<p>Don\'t forget traditional Nepali drinks! Chyang (rice beer) and raksi (distilled alcohol) are authentic pairings that have been enjoyed for generations.</p>\r\n\r\n<h3>Non-Alcoholic Options</h3>\r\n<p>Our homemade lassi, masala tea, and fresh fruit juices provide excellent non-alcoholic pairing options.</p>\r\n\r\n<p>Ask our staff for pairing recommendations during your next visit!</p>', 'images/blogs/demoBlog.jpg', 483, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(12, 'Behind Every Great Meal: Our Service Philosophy', 'Service', 'service, hospitality, team, customer care', '<h2>Service Excellence is Our Promise</h2>\r\n<p>At Hotel Annapurna, we believe that great food deserves equally great service. Here\'s what sets us apart.</p>\r\n\r\n<h3>Training and Development</h3>\r\n<p>Every team member undergoes comprehensive training covering food knowledge, service techniques, and cultural sensitivity. We invest in continuous education to maintain high standards.</p>\r\n\r\n<h3>Attention to Detail</h3>\r\n<p>From remembering regular guests\' preferences to anticipating needs before they\'re expressed, our staff pays attention to every detail.</p>\r\n\r\n<h3>Nepali Hospitality</h3>\r\n<p>We embrace the Nepali concept of \"Atithi Devo Bhava\" (Guest is God). This ancient principle guides every interaction with our guests.</p>\r\n\r\n<h3>Feedback Culture</h3>\r\n<p>We actively seek and value guest feedback. Every comment helps us improve and serves as a learning opportunity for our team.</p>\r\n\r\n<h3>Team Spirit</h3>\r\n<p>Our service excellence comes from a cohesive team that works together seamlessly. From kitchen to dining room, everyone plays a crucial role.</p>\r\n\r\n<p>Experience the difference that genuine care makes!</p>', 'images/blogs/demoBlog.jpg', 154, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(13, 'Exploring Kathmandu: Your Guide to the City', 'Travel', 'kathmandu, travel, tourism, guide', '<h2>Discover Kathmandu with Hotel Annapurna</h2>\r\n<p>Staying at Hotel Annapurna puts you at the center of Kathmandu\'s rich cultural heritage. Here\'s your guide to exploring the city.</p>\r\n\r\n<h3>Must-Visit Sites</h3>\r\n<p>Durbar Square, just 15 minutes away, showcases ancient palaces and temples. Swayambhunath (Monkey Temple) offers panoramic city views. Pashupatinath and Boudhanath are essential spiritual sites.</p>\r\n\r\n<h3>Shopping Districts</h3>\r\n<p>Thamel, the tourist hub, is perfect for souvenirs and trekking gear. New Road offers modern shopping experiences. Don\'t miss the traditional markets in Asan.</p>\r\n\r\n<h3>Dining Adventures</h3>\r\n<p>While we hope you\'ll dine with us often, Kathmandu has diverse culinary options. Try local eateries for street food and fine dining restaurants for fusion cuisine.</p>\r\n\r\n<h3>Day Trips</h3>\r\n<p>Bhaktapur and Patan, ancient cities with stunning architecture, make excellent day trips. Nagarkot offers sunrise views of the Himalayas.</p>\r\n\r\n<h3>Cultural Experiences</h3>\r\n<p>Attend traditional dance performances, visit pottery workshops, or take a cooking class to immerse yourself in Nepali culture.</p>\r\n\r\n<p>Our concierge can help plan your Kathmandu adventure!</p>', 'images/blogs/demoBlog.jpg', 467, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(14, 'The Secret Life of Spices in Nepali Cooking', 'Cooking Tips', 'spices, ingredients, cooking, flavors', '<h2>Understanding the Spice Box</h2>\r\n<p>Spices are the soul of Nepali cuisine. Let\'s explore the essential spices and how we use them at Hotel Annapurna.</p>\r\n\r\n<h3>Turmeric (Besar)</h3>\r\n<p>Beyond adding golden color, turmeric has anti-inflammatory properties. We use it in almost every curry and dal preparation.</p>\r\n\r\n<h3>Cumin (Jeera)</h3>\r\n<p>Both whole and ground cumin feature prominently in our cooking. Toasted cumin seeds add incredible depth to dishes.</p>\r\n\r\n<h3>Coriander (Dhaniya)</h3>\r\n<p>Fresh coriander leaves garnish most dishes, while ground coriander powder is essential in curry bases.</p>\r\n\r\n<h3>Timur (Sichuan Pepper)</h3>\r\n<p>This unique Himalayan spice creates a tingling sensation on the tongue. It\'s essential in authentic chhoila and achar.</p>\r\n\r\n<h3>Cardamom (Alainchi)</h3>\r\n<p>Both green and black cardamom are used. Green for sweets and tea, black for savory dishes and biryanis.</p>\r\n\r\n<h3>Ginger and Garlic</h3>\r\n<p>Fresh ginger-garlic paste forms the foundation of countless dishes. We prepare it fresh daily for maximum flavor.</p>\r\n\r\n<p>Each spice tells a story and adds character to our dishes!</p>', 'images/blogs/demoBlog.jpg', 450, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(15, 'Corporate Events and Business Dining at Hotel Annapurna', 'Business', 'corporate, events, meetings, business', '<h2>Your Partner for Business Success</h2>\r\n<p>Hotel Annapurna provides the perfect setting for corporate events, business meetings, and professional gatherings.</p>\r\n\r\n<h3>Meeting Facilities</h3>\r\n<p>Our conference rooms are equipped with modern AV equipment, high-speed internet, and comfortable seating. Capacity ranges from intimate 10-person meetings to 100+ person conferences.</p>\r\n\r\n<h3>Business Lunch Packages</h3>\r\n<p>Our express lunch menu is designed for busy professionals. Quality meals served promptly without compromising taste or presentation.</p>\r\n\r\n<h3>Catering Services</h3>\r\n<p>We provide full-service catering for your office events, from working lunches to formal dinners. Custom menus available to suit your needs and budget.</p>\r\n\r\n<h3>Private Dining Rooms</h3>\r\n<p>For important client dinners or team celebrations, our private dining rooms offer exclusivity with personalized service.</p>\r\n\r\n<h3>Corporate Packages</h3>\r\n<p>We offer special corporate rates for regular business clients. Contact us to learn about our membership benefits.</p>\r\n\r\n<p>Make Hotel Annapurna your business dining destination!</p>', 'images/blogs/demoBlog.jpg', 344, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(16, 'From Farm to Table: Our Ingredient Journey', 'Sustainability', 'farm, local, fresh, ingredients', '<h2>Tracing Our Ingredients</h2>\r\n<p>Ever wondered where your food comes from? At Hotel Annapurna, we\'re proud of our ingredient sourcing story.</p>\r\n\r\n<h3>Local Farmers Partnership</h3>\r\n<p>We work directly with farmers in nearby villages. This relationship ensures we get the freshest produce while farmers receive fair prices.</p>\r\n\r\n<h3>Seasonal Menus</h3>\r\n<p>Our menu changes with seasons to feature the best available ingredients. Spring brings fresh greens, summer offers abundant vegetables, and winter provides hearty root vegetables.</p>\r\n\r\n<h3>Quality Control</h3>\r\n<p>Every ingredient is inspected upon arrival. We maintain strict quality standards to ensure only the best makes it to your plate.</p>\r\n\r\n<h3>Dairy and Meat</h3>\r\n<p>Our dairy products come from local cooperatives. Meat is sourced from certified suppliers who follow ethical and hygienic practices.</p>\r\n\r\n<h3>Herbs and Spices</h3>\r\n<p>Many herbs are grown in our rooftop garden. Spices are sourced from trusted suppliers who provide authentic, unadulterated products.</p>\r\n\r\n<p>Taste the difference that quality ingredients make!</p>', 'images/blogs/demoBlog.jpg', 416, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(17, 'Weekend Brunch: A New Tradition at Hotel Annapurna', 'Restaurant', 'brunch, weekend, dining, special', '<h2>Introducing Our Weekend Brunch Experience</h2>\r\n<p>We\'re excited to announce our new weekend brunch menu, combining international favorites with Nepali specialties.</p>\r\n\r\n<h3>Brunch Menu Highlights</h3>\r\n<p>Choose from fluffy pancakes, eggs benedict, fresh fruit platters, traditional Nepali breakfast items, and our special brunch thali.</p>\r\n\r\n<h3>Live Cooking Stations</h3>\r\n<p>Watch our chefs prepare made-to-order omelets, dosas, and momos right in front of you. Interactive cooking adds excitement to your meal.</p>\r\n\r\n<h3>Beverage Bar</h3>\r\n<p>Unlimited coffee, tea, fresh juices, and lassi included with every brunch order. Add mimosas or cocktails for a festive touch.</p>\r\n\r\n<h3>Family-Friendly</h3>\r\n<p>Kids eat at discounted rates, and we have special children\'s menu items. Our spacious dining area accommodates families comfortably.</p>\r\n\r\n<h3>Timing and Reservations</h3>\r\n<p>Brunch is served every Saturday and Sunday from 10 AM to 2 PM. Advance reservations recommended, especially for large groups.</p>\r\n\r\n<p>Join us this weekend for a memorable brunch experience!</p>', 'images/blogs/demoBlog.jpg', 184, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(18, 'The Art of Hospitality: Lessons from Nepal', 'Culture', 'hospitality, culture, tradition, nepal', '<h2>What Makes Nepali Hospitality Special</h2>\r\n<p>Nepali hospitality is world-renowned, and it\'s deeply rooted in culture and tradition. Here\'s what we can learn from it.</p>\r\n\r\n<h3>Warm Welcomes</h3>\r\n<p>In Nepali culture, guests are greeted with genuine warmth. The traditional \"Namaste\" is more than a greeting – it\'s a gesture of respect and welcome.</p>\r\n\r\n<h3>Generosity</h3>\r\n<p>Nepalis believe in serving the best to guests, even if it means the host goes without. This spirit of generosity permeates every interaction at Hotel Annapurna.</p>\r\n\r\n<h3>Personal Connection</h3>\r\n<p>Unlike transactional service, Nepali hospitality focuses on building genuine connections. Our staff takes time to know guests personally.</p>\r\n\r\n<h3>Attention to Comfort</h3>\r\n<p>Ensuring guest comfort is paramount. From adjusting spice levels to remembering dietary preferences, we go the extra mile.</p>\r\n\r\n<h3>Community Spirit</h3>\r\n<p>Nepali hospitality extends beyond individual service to creating a sense of community. At our hotel, guests often become part of our extended family.</p>\r\n\r\n<p>Experience authentic Nepali hospitality at Hotel Annapurna!</p>', 'images/blogs/demoBlog.jpg', 490, 2, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(19, 'Desserts of Nepal: Sweet Endings to Remember', 'Food & Culture', 'desserts, sweets, nepali food, traditional', '<h2>Exploring Nepal\'s Sweet Traditions</h2>\r\n<p>Nepali desserts are diverse and delicious, reflecting influences from India, Tibet, and indigenous traditions.</p>\r\n\r\n<h3>Sel Roti</h3>\r\n<p>This ring-shaped rice bread is crispy outside and soft inside. Traditionally made during festivals, we now offer it year-round with our special recipe.</p>\r\n\r\n<h3>Jeri (Jalebi)</h3>\r\n<p>Deep-fried batter soaked in sugar syrup, jeri is a festival favorite. Our version is made fresh daily and served warm.</p>\r\n\r\n<h3>Kheer (Rice Pudding)</h3>\r\n<p>Creamy rice pudding flavored with cardamom and garnished with nuts. Comfort food at its finest.</p>\r\n\r\n<h3>Sikarni</h3>\r\n<p>Sweetened thick yogurt mixed with nuts and dried fruits. A cooling dessert perfect for hot days.</p>\r\n\r\n<h3>Barfi</h3>\r\n<p>Milk-based confection in various flavors. Our chef makes coconut, pistachio, and cardamom varieties.</p>\r\n\r\n<h3>Modern Fusion</h3>\r\n<p>We also create fusion desserts combining traditional flavors with contemporary techniques. Try our cardamom panna cotta or timur-infused chocolate mousse.</p>\r\n\r\n<p>Save room for dessert at Hotel Annapurna!</p>', 'images/blogs/demoBlog.jpg', 350, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(20, 'Your Feedback Matters: How We Continuously Improve', 'Customer Service', 'feedback, improvement, quality, service', '<h2>Building Excellence Through Your Input</h2>\r\n<p>At Hotel Annapurna, we believe that continuous improvement is key to maintaining excellence. Your feedback drives our progress.</p>\r\n\r\n<h3>Multiple Feedback Channels</h3>\r\n<p>Share your experience through comment cards, online reviews, email, or speak directly to our managers. We read every piece of feedback.</p>\r\n\r\n<h3>Quick Response</h3>\r\n<p>We respond to all feedback within 24 hours. Positive or negative, every comment receives attention and thoughtful response.</p>\r\n\r\n<h3>Actionable Changes</h3>\r\n<p>Guest suggestions have led to menu additions, service improvements, and facility upgrades. Your voice directly shapes our evolution.</p>\r\n\r\n<h3>Recognition and Rewards</h3>\r\n<p>Regular guests and those who provide valuable feedback are recognized through our loyalty program with special discounts and exclusive offers.</p>\r\n\r\n<h3>Transparency</h3>\r\n<p>We share how we\'re addressing concerns and implementing suggestions. Quarterly reports detail improvements made based on guest feedback.</p>\r\n\r\n<h3>Staff Training</h3>\r\n<p>Feedback is incorporated into staff training programs, ensuring the entire team learns from guest experiences.</p>\r\n\r\n<p>Thank you for helping us become better every day!</p>', 'images/blogs/demoBlog.jpg', 328, 1, 'published', '2025-12-07 08:32:20', '2025-12-07 08:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `blog_interactions`
--

CREATE TABLE `blog_interactions` (
  `id` int(11) NOT NULL,
  `blog_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `interaction_type` enum('like','comment','share') NOT NULL,
  `comment_text` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_type` enum('food','room','table') NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`item_data`)),
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_requests`
--

CREATE TABLE `contact_requests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','in-progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_purchase` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `valid_from` datetime NOT NULL,
  `valid_until` datetime NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_purchase`, `max_discount`, `usage_limit`, `used_count`, `valid_from`, `valid_until`, `status`, `created_at`, `updated_at`) VALUES
(1, 'WELCOME10', 'percentage', 10.00, 500.00, 100.00, 100, 0, '2024-12-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(2, 'NEWUSER20', 'percentage', 20.00, 1000.00, 200.00, 50, 0, '2024-12-01 00:00:00', '2025-06-30 23:59:59', 'active', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(3, 'SAVE50', 'fixed', 50.00, 300.00, NULL, 200, 0, '2024-12-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(4, 'FOOD15', 'percentage', 15.00, 500.00, 150.00, 150, 0, '2024-12-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(5, 'WEEKEND25', 'percentage', 25.00, 1500.00, 300.00, 75, 0, '2024-12-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(6, 'ROOM100', 'fixed', 100.00, 2000.00, NULL, 80, 0, '2024-12-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(7, 'FAMILY30', 'percentage', 30.00, 2500.00, 500.00, 60, 0, '2024-12-01 00:00:00', '2025-12-31 23:59:59', 'active', '2025-12-07 08:32:20', '2025-12-07 08:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `category` enum('veg','non-veg','special') NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `available_days` varchar(200) DEFAULT 'All Days',
  `short_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `category`, `food_name`, `price`, `discount_price`, `image_path`, `available_days`, `short_description`, `created_at`, `updated_at`) VALUES
(1, 'veg', 'Vegetable Momo (10 pcs)', 180.00, 150.00, 'images/menu/demoFood.jpg', 'All Days', 'Steamed dumplings filled with fresh vegetables and Himalayan spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(2, 'veg', 'Paneer Butter Masala', 350.00, 320.00, 'images/menu/demoFood.jpg', 'All Days', 'Cottage cheese cubes in rich tomato and butter gravy', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(3, 'veg', 'Dal Bhat Tarkari', 250.00, 220.00, 'images/menu/demoFood.jpg', 'All Days', 'Traditional Nepali lentil soup with rice and vegetable curry', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(4, 'veg', 'Mixed Vegetable Thali', 300.00, 280.00, 'images/menu/demoFood.jpg', 'All Days', 'Complete meal with rice, dal, vegetables, pickle, and papad', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(5, 'veg', 'Mushroom Chhoila', 280.00, 250.00, 'images/menu/demoFood.jpg', 'All Days', 'Spicy grilled mushroom in Newari style with authentic spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(6, 'veg', 'Veg Fried Rice', 220.00, 200.00, 'images/menu/demoFood.jpg', 'All Days', 'Aromatic rice stir-fried with fresh vegetables and soy sauce', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(7, 'veg', 'Palak Paneer', 340.00, 310.00, 'images/menu/demoFood.jpg', 'All Days', 'Cottage cheese cubes in creamy spinach gravy', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(8, 'non-veg', 'Chicken Momo (10 pcs)', 220.00, 200.00, 'images/menu/demoFood.jpg', 'All Days', 'Juicy chicken dumplings steamed to perfection', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(9, 'non-veg', 'Chicken Chhoila', 380.00, 350.00, 'images/menu/demoFood.jpg', 'All Days', 'Grilled chicken marinated in Newari spices, served with beaten rice', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(10, 'non-veg', 'Butter Chicken', 420.00, 390.00, 'images/menu/demoFood.jpg', 'All Days', 'Tender chicken pieces in creamy tomato butter sauce', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(11, 'non-veg', 'Chicken Biryani', 400.00, 370.00, 'images/menu/demoFood.jpg', 'All Days', 'Aromatic basmati rice layered with spiced chicken', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(12, 'non-veg', 'Fish Fry', 450.00, 420.00, 'images/menu/demoFood.jpg', 'Monday,Wednesday,Friday', 'Crispy fried fish marinated in special herbs and spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(13, 'non-veg', 'Mutton Curry', 550.00, 520.00, 'images/menu/demoFood.jpg', 'All Days', 'Slow-cooked mutton in traditional Nepali spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(14, 'non-veg', 'Chicken Sekuwa', 360.00, 330.00, 'images/menu/demoFood.jpg', 'All Days', 'Barbecued chicken skewers with authentic Nepali marinade', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(15, 'special', 'Newari Khaja Set', 650.00, 600.00, 'images/menu/demoFood.jpg', 'Friday,Saturday,Sunday', 'Authentic Newari platter with beaten rice, bara, choila, and achar', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(16, 'special', 'Thakali Thali', 700.00, 650.00, 'images/menu/demoFood.jpg', 'All Days', 'Complete Thakali meal with rice, dal, tarkari, gundruk, and achar', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(17, 'special', 'Tandoori Chicken (Full)', 850.00, 800.00, 'images/menu/demoFood.jpg', 'All Days', 'Whole chicken marinated in yogurt and spices, cooked in tandoor', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(18, 'special', 'Mixed Grill Platter', 950.00, 900.00, 'images/menu/demoFood.jpg', 'Friday,Saturday,Sunday', 'Assorted grilled meats including chicken, mutton, and fish', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(19, 'special', 'Seafood Special', 1200.00, 1150.00, 'images/menu/demoFood.jpg', 'Saturday,Sunday', 'Chef\'s special seafood combination with prawns, fish, and calamari', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(20, 'special', 'Royal Annapurna Feast', 1500.00, 1400.00, 'images/menu/demoFood.jpg', 'All Days', 'Grand feast with multiple courses including appetizers, mains, and desserts', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(21, 'veg', 'Aloo Gobi Masala', 260.00, 240.00, 'images/menu/demoFood.jpg', 'All Days', 'Potato and cauliflower curry with aromatic spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(22, 'veg', 'Saag Paneer', 330.00, 300.00, 'images/menu/demoFood.jpg', 'All Days', 'Fresh greens with cottage cheese in mild spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(23, 'veg', 'Chana Masala', 240.00, 220.00, 'images/menu/demoFood.jpg', 'All Days', 'Chickpeas cooked in tangy tomato onion gravy', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(24, 'veg', 'Baingan Bharta', 270.00, 250.00, 'images/menu/demoFood.jpg', 'All Days', 'Roasted eggplant mashed with spices and herbs', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(25, 'veg', 'Veg Spring Roll (6 pcs)', 200.00, 180.00, 'images/menu/demoFood.jpg', 'All Days', 'Crispy rolls filled with mixed vegetables', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(26, 'veg', 'Mushroom Fried Rice', 240.00, 220.00, 'images/menu/demoFood.jpg', 'All Days', 'Fried rice with fresh mushrooms and vegetables', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(27, 'veg', 'Veg Noodles', 230.00, 210.00, 'images/menu/demoFood.jpg', 'All Days', 'Stir-fried noodles with seasonal vegetables', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(28, 'non-veg', 'Chicken Curry', 380.00, 350.00, 'images/menu/demoFood.jpg', 'All Days', 'Traditional chicken curry with home-style spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(29, 'non-veg', 'Chicken Fried Rice', 280.00, 260.00, 'images/menu/demoFood.jpg', 'All Days', 'Fragrant rice with chicken and vegetables', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(30, 'non-veg', 'Pork Chhoila', 420.00, 390.00, 'images/menu/demoFood.jpg', 'All Days', 'Grilled pork in authentic Newari marinade', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(31, 'non-veg', 'Chicken Noodles', 270.00, 250.00, 'images/menu/demoFood.jpg', 'All Days', 'Wok-tossed noodles with juicy chicken pieces', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(32, 'non-veg', 'Mutton Sekuwa', 580.00, 550.00, 'images/menu/demoFood.jpg', 'All Days', 'Grilled mutton skewers with traditional spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(33, 'non-veg', 'Prawn Curry', 650.00, 620.00, 'images/menu/demoFood.jpg', 'Monday,Wednesday,Friday', 'Succulent prawns in rich coconut gravy', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(34, 'non-veg', 'Chicken Thukpa', 320.00, 300.00, 'images/menu/demoFood.jpg', 'All Days', 'Hearty noodle soup with chicken and vegetables', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(35, 'special', 'Lamb Rogan Josh', 800.00, 750.00, 'images/menu/demoFood.jpg', 'All Days', 'Aromatic lamb curry from Kashmir with rich spices', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(36, 'special', 'Prawn Biryani', 750.00, 700.00, 'images/menu/demoFood.jpg', 'Friday,Saturday,Sunday', 'Premium biryani with fresh prawns and aromatic rice', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(37, 'special', 'Duck Roast', 950.00, 900.00, 'images/menu/demoFood.jpg', 'Saturday,Sunday', 'Slow-roasted duck with herbs and vegetables', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(38, 'special', 'Lobster Thermidor', 1800.00, 1700.00, 'images/menu/demoFood.jpg', 'Saturday,Sunday', 'Luxurious lobster in creamy sauce', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(39, 'special', 'Chef Special BBQ', 1100.00, 1050.00, 'images/menu/demoFood.jpg', 'Friday,Saturday,Sunday', 'Mixed BBQ platter with chef\'s secret marinade', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(40, 'special', 'Himalayan Yak Steak', 1300.00, 1250.00, 'images/menu/demoFood.jpg', 'All Days', 'Premium yak meat steak with mountain herbs', '2025-12-07 08:32:20', '2025-12-07 08:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_type` enum('food','room','table') NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','esewa','khalti','card') DEFAULT 'cash',
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `booking_reference` varchar(50) DEFAULT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `token` varchar(128) NOT NULL,
  `expiry` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `is_expired` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registration_otps`
--

CREATE TABLE `registration_otps` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expiry` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `is_expired` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `room_no` varchar(50) NOT NULL,
  `room_type` enum('single','double','deluxe','suite') DEFAULT 'single',
  `total_beds` int(11) NOT NULL,
  `bed_size` enum('single','double','queen','king') DEFAULT 'double',
  `status` enum('available','booked','reserved','maintenance','occupied') DEFAULT 'available',
  `price` decimal(10,2) NOT NULL,
  `price_today` decimal(10,2) DEFAULT NULL,
  `amenities` text DEFAULT NULL,
  `short_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `image_path`, `room_no`, `room_type`, `total_beds`, `bed_size`, `status`, `price`, `price_today`, `amenities`, `short_description`, `created_at`, `updated_at`) VALUES
(1, 'images/rooms/demoRoom.jpg', 'R-101', 'single', 1, 'single', 'available', 1500.00, 1350.00, 'Free WiFi, AC, TV, Mini Fridge', 'Compact single room with modern amenities', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(2, 'images/rooms/demoRoom.jpg', 'R-102', 'single', 1, 'single', 'available', 1500.00, 1350.00, 'Free WiFi, AC, TV, Work Desk', 'Perfect for solo business travelers', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(3, 'images/rooms/demoRoom.jpg', 'R-103', 'single', 1, 'double', 'occupied', 1800.00, 1650.00, 'Free WiFi, AC, TV, Mini Fridge, Balcony', 'Single room with double bed and mountain view', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(4, 'images/rooms/demoRoom.jpg', 'R-201', 'double', 2, 'double', 'available', 2500.00, 2300.00, 'Free WiFi, AC, TV, Mini Fridge, Coffee Maker', 'Spacious double room with twin beds', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(5, 'images/rooms/demoRoom.jpg', 'R-202', 'double', 2, 'queen', 'available', 2800.00, 2600.00, 'Free WiFi, AC, TV, Mini Fridge, Balcony, City View', 'Comfortable double room with queen bed', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(6, 'images/rooms/demoRoom.jpg', 'R-203', 'double', 2, 'double', 'booked', 2500.00, 2300.00, 'Free WiFi, AC, TV, Work Area', 'Modern double room perfect for couples', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(7, 'images/rooms/demoRoom.jpg', 'R-204', 'double', 2, 'queen', 'available', 2800.00, 2600.00, 'Free WiFi, AC, TV, Mini Bar, Mountain View', 'Elegant room with stunning views', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(8, 'images/rooms/demoRoom.jpg', 'R-205', 'double', 2, 'king', 'available', 3200.00, 3000.00, 'Free WiFi, AC, Smart TV, Mini Bar, Balcony', 'Luxurious double room with king-size bed', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(9, 'images/rooms/demoRoom.jpg', 'R-301', 'deluxe', 2, 'king', 'available', 4000.00, 3800.00, 'Free WiFi, AC, Smart TV, Mini Bar, Jacuzzi, Living Area', 'Premium deluxe room with premium amenities', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(10, 'images/rooms/demoRoom.jpg', 'R-302', 'deluxe', 3, 'king', 'available', 4500.00, 4200.00, 'Free WiFi, AC, Smart TV, Mini Bar, Sofa Bed, Balcony', 'Deluxe room with extra bed option', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(11, 'images/rooms/demoRoom.jpg', 'R-303', 'deluxe', 2, 'king', 'maintenance', 4000.00, 3800.00, 'Free WiFi, AC, Smart TV, Mini Bar, City View', 'Currently under renovation', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(12, 'images/rooms/demoRoom.jpg', 'R-304', 'deluxe', 2, 'king', 'available', 4200.00, 4000.00, 'Free WiFi, AC, Smart TV, Mini Bar, Walk-in Closet', 'Deluxe room with spacious wardrobe', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(13, 'images/rooms/demoRoom.jpg', 'R-305', 'deluxe', 2, 'king', 'available', 4300.00, 4100.00, 'Free WiFi, AC, Smart TV, Mini Bar, Garden View, Terrace', 'Deluxe room with private terrace', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(14, 'images/rooms/demoRoom.jpg', 'R-401', 'suite', 3, 'king', 'available', 6000.00, 5700.00, 'Free WiFi, AC, Smart TV, Full Kitchen, Living Room, Dining Area', 'Executive suite perfect for long stays', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(15, 'images/rooms/demoRoom.jpg', 'R-402', 'suite', 4, 'king', 'available', 7000.00, 6700.00, 'Free WiFi, AC, Smart TV, Kitchenette, 2 Bathrooms, Balcony', 'Family suite with two bedrooms', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(16, 'images/rooms/demoRoom.jpg', 'R-403', 'suite', 3, 'king', 'booked', 6500.00, 6200.00, 'Free WiFi, AC, Smart TV, Mini Bar, Jacuzzi, Mountain View', 'Luxury suite with panoramic views', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(17, 'images/rooms/demoRoom.jpg', 'R-404', 'suite', 4, 'king', 'available', 7500.00, 7200.00, 'Free WiFi, AC, Smart TV, Full Kitchen, Living Room, 2 Balconies', 'Spacious suite ideal for families', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(18, 'images/rooms/demoRoom.jpg', 'R-501', 'suite', 5, 'king', 'available', 10000.00, 9500.00, 'Free WiFi, AC, Smart TV, Full Kitchen, 2 Bathrooms, Terrace, Butler Service', 'Presidential suite with VIP services', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(19, 'images/rooms/demoRoom.jpg', 'R-502', 'suite', 4, 'king', 'available', 8000.00, 7600.00, 'Free WiFi, AC, Smart TV, Dining Room, Study Room, Premium Toiletries', 'Grand suite with business facilities', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(20, 'images/rooms/demoRoom.jpg', 'R-503', 'suite', 6, 'king', 'available', 12000.00, 11500.00, 'Free WiFi, AC, Smart TV, Full Kitchen, 3 Bedrooms, 2.5 Baths, Rooftop Access', 'Penthouse suite with ultimate luxury', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(21, 'images/rooms/demoRoom.jpg', 'R-104', 'single', 1, 'single', 'available', 1550.00, 1400.00, 'Free WiFi, AC, TV, Safe Box', 'Cozy single with extra security', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(22, 'images/rooms/demoRoom.jpg', 'R-105', 'single', 1, 'double', 'available', 1850.00, 1700.00, 'Free WiFi, AC, TV, Mini Fridge, Coffee Maker', 'Single with premium amenities', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(23, 'images/rooms/demoRoom.jpg', 'R-206', 'double', 2, 'queen', 'available', 2900.00, 2700.00, 'Free WiFi, AC, Smart TV, Mini Bar, Balcony', 'Double room with premium views', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(24, 'images/rooms/demoRoom.jpg', 'R-207', 'double', 2, 'double', 'available', 2600.00, 2400.00, 'Free WiFi, AC, TV, Work Desk, Safe', 'Business traveler double room', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(25, 'images/rooms/demoRoom.jpg', 'R-208', 'double', 2, 'king', 'booked', 3300.00, 3100.00, 'Free WiFi, AC, Smart TV, Mini Bar, Bathtub', 'Luxury double with spa bath', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(26, 'images/rooms/demoRoom.jpg', 'R-209', 'double', 2, 'queen', 'available', 2850.00, 2650.00, 'Free WiFi, AC, TV, Coffee Maker, Garden View', 'Peaceful garden-facing room', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(27, 'images/rooms/demoRoom.jpg', 'R-210', 'double', 2, 'king', 'available', 3400.00, 3200.00, 'Free WiFi, AC, Smart TV, Mini Bar, Sitting Area', 'Spacious double with lounge', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(28, 'images/rooms/demoRoom.jpg', 'R-306', 'deluxe', 2, 'king', 'available', 4400.00, 4200.00, 'Free WiFi, AC, Smart TV, Mini Bar, Jacuzzi, City Lights', 'Deluxe with skyline views', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(29, 'images/rooms/demoRoom.jpg', 'R-307', 'deluxe', 3, 'king', 'available', 4700.00, 4500.00, 'Free WiFi, AC, Smart TV, Mini Bar, Sofa Bed, 2 Balconies', 'Deluxe suite with dual balconies', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(30, 'images/rooms/demoRoom.jpg', 'R-308', 'deluxe', 2, 'king', 'available', 4250.00, 4050.00, 'Free WiFi, AC, Smart TV, Mini Bar, Steam Room', 'Deluxe with private steam bath', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(31, 'images/rooms/demoRoom.jpg', 'R-309', 'deluxe', 2, 'king', 'maintenance', 4000.00, 3800.00, 'Free WiFi, AC, Smart TV, Mini Bar', 'Currently being upgraded', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(32, 'images/rooms/demoRoom.jpg', 'R-310', 'deluxe', 3, 'king', 'available', 4800.00, 4600.00, 'Free WiFi, AC, Smart TV, Mini Bar, Office Space', 'Deluxe business suite', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(33, 'images/rooms/demoRoom.jpg', 'R-504', 'suite', 4, 'king', 'available', 8500.00, 8100.00, 'Free WiFi, AC, Smart TV, Full Kitchen, 2 Bedrooms, Gym Access', 'Family suite with fitness', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(34, 'images/rooms/demoRoom.jpg', 'R-505', 'suite', 3, 'king', 'available', 7200.00, 6900.00, 'Free WiFi, AC, Smart TV, Kitchenette, Private Pool', 'Suite with exclusive pool', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(35, 'images/rooms/demoRoom.jpg', 'R-506', 'suite', 5, 'king', 'available', 9500.00, 9100.00, 'Free WiFi, AC, Smart TV, Full Kitchen, Cinema Room', 'Entertainment suite with theater', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(36, 'images/rooms/demoRoom.jpg', 'R-507', 'suite', 4, 'king', 'booked', 8200.00, 7800.00, 'Free WiFi, AC, Smart TV, Full Kitchen, Spa Bath', 'Wellness suite with spa', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(37, 'images/rooms/demoRoom.jpg', 'R-508', 'suite', 6, 'king', 'available', 11000.00, 10500.00, 'Free WiFi, AC, Smart TV, Full Kitchen, 2 Living Rooms, Library', 'Luxury suite with reading room', '2025-12-07 08:32:20', '2025-12-07 08:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `table_no` varchar(50) NOT NULL,
  `total_chairs` int(11) NOT NULL,
  `booking_status` enum('available','booked','reserved','maintenance','occupied') DEFAULT 'available',
  `price_main` decimal(10,2) NOT NULL,
  `price_today` decimal(10,2) DEFAULT NULL,
  `location` enum('ground floor','first floor','outside','inside') DEFAULT 'ground floor',
  `short_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `image_path`, `table_no`, `total_chairs`, `booking_status`, `price_main`, `price_today`, `location`, `short_description`, `created_at`, `updated_at`) VALUES
(1, 'images/tables/demoTable.jpg', 'T-101', 2, 'available', 500.00, 450.00, 'ground floor', 'Cozy corner table perfect for couples', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(2, 'images/tables/demoTable.jpg', 'T-102', 4, 'available', 800.00, 750.00, 'ground floor', 'Family-friendly table near the window', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(3, 'images/tables/demoTable.jpg', 'T-103', 4, 'available', 800.00, 750.00, 'ground floor', 'Comfortable seating with garden view', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(4, 'images/tables/demoTable.jpg', 'T-104', 6, 'available', 1200.00, 1100.00, 'ground floor', 'Large table ideal for small gatherings', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(5, 'images/tables/demoTable.jpg', 'T-105', 2, 'booked', 500.00, 450.00, 'ground floor', 'Intimate setting by the indoor fountain', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(6, 'images/tables/demoTable.jpg', 'T-106', 4, 'available', 850.00, 800.00, 'outside', 'Outdoor patio seating with mountain views', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(7, 'images/tables/demoTable.jpg', 'T-107', 4, 'available', 850.00, 800.00, 'outside', 'Garden terrace table under the pergola', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(8, 'images/tables/demoTable.jpg', 'T-108', 6, 'available', 1250.00, 1150.00, 'outside', 'Spacious outdoor table for family dining', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(9, 'images/tables/demoTable.jpg', 'T-109', 2, 'available', 550.00, 500.00, 'outside', 'Romantic balcony seating', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(10, 'images/tables/demoTable.jpg', 'T-110', 8, 'reserved', 1600.00, 1500.00, 'outside', 'Premium outdoor table for special occasions', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(11, 'images/tables/demoTable.jpg', 'T-201', 2, 'available', 600.00, 550.00, 'first floor', 'Private booth with cushioned seating', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(12, 'images/tables/demoTable.jpg', 'T-202', 4, 'available', 900.00, 850.00, 'first floor', 'Executive table with city views', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(13, 'images/tables/demoTable.jpg', 'T-203', 4, 'available', 900.00, 850.00, 'first floor', 'Elegant setting near the fireplace', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(14, 'images/tables/demoTable.jpg', 'T-204', 6, 'available', 1300.00, 1200.00, 'first floor', 'Premium table in VIP section', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(15, 'images/tables/demoTable.jpg', 'T-205', 8, 'available', 1700.00, 1600.00, 'first floor', 'Large family table with traditional décor', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(16, 'images/tables/demoTable.jpg', 'T-206', 10, 'available', 2000.00, 1900.00, 'first floor', 'Banquet table for celebrations and events', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(17, 'images/tables/demoTable.jpg', 'T-207', 4, 'maintenance', 900.00, 850.00, 'first floor', 'Temporarily under maintenance', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(18, 'images/tables/demoTable.jpg', 'T-208', 6, 'available', 1350.00, 1250.00, 'first floor', 'Corner table with panoramic windows', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(19, 'images/tables/demoTable.jpg', 'T-209', 2, 'available', 650.00, 600.00, 'first floor', 'Luxury private dining nook', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(20, 'images/tables/demoTable.jpg', 'T-210', 12, 'available', 2500.00, 2300.00, 'first floor', 'Grand table for large parties and business meetings', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(21, 'images/tables/demoTable.jpg', 'T-111', 4, 'available', 850.00, 800.00, 'ground floor', 'Modern table with ambient lighting', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(22, 'images/tables/demoTable.jpg', 'T-112', 2, 'available', 520.00, 480.00, 'ground floor', 'Quiet corner for intimate dining', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(23, 'images/tables/demoTable.jpg', 'T-113', 6, 'available', 1220.00, 1120.00, 'ground floor', 'Family table near the buffet area', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(24, 'images/tables/demoTable.jpg', 'T-114', 4, 'booked', 880.00, 830.00, 'ground floor', 'Premium spot with central location', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(25, 'images/tables/demoTable.jpg', 'T-115', 8, 'available', 1650.00, 1550.00, 'ground floor', 'Large table for group celebrations', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(26, 'images/tables/demoTable.jpg', 'T-116', 2, 'available', 580.00, 530.00, 'outside', 'Rooftop seating with sunset views', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(27, 'images/tables/demoTable.jpg', 'T-117', 4, 'available', 900.00, 850.00, 'outside', 'Garden table surrounded by flowers', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(28, 'images/tables/demoTable.jpg', 'T-118', 6, 'reserved', 1280.00, 1200.00, 'outside', 'Premium terrace spot for special occasions', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(29, 'images/tables/demoTable.jpg', 'T-119', 4, 'available', 920.00, 870.00, 'outside', 'Poolside dining table', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(30, 'images/tables/demoTable.jpg', 'T-120', 10, 'available', 1850.00, 1750.00, 'outside', 'Large patio table for parties', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(31, 'images/tables/demoTable.jpg', 'T-211', 4, 'available', 950.00, 900.00, 'first floor', 'Business meeting table with projector access', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(32, 'images/tables/demoTable.jpg', 'T-212', 2, 'available', 680.00, 630.00, 'first floor', 'Romantic corner with dim lighting', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(33, 'images/tables/demoTable.jpg', 'T-213', 6, 'available', 1380.00, 1280.00, 'first floor', 'VIP section with exclusive service', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(34, 'images/tables/demoTable.jpg', 'T-214', 8, 'available', 1750.00, 1650.00, 'first floor', 'Premium dining with mountain views', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(35, 'images/tables/demoTable.jpg', 'T-215', 4, 'maintenance', 900.00, 850.00, 'first floor', 'Under renovation for upgrades', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(36, 'images/tables/demoTable.jpg', 'T-216', 14, 'available', 2800.00, 2600.00, 'first floor', 'Grand banquet table for large events', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(37, 'images/tables/demoTable.jpg', 'T-217', 2, 'available', 700.00, 650.00, 'first floor', 'Executive booth with privacy screen', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(38, 'images/tables/demoTable.jpg', 'T-218', 6, 'available', 1400.00, 1300.00, 'first floor', 'Corner table with wine cellar view', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(39, 'images/tables/demoTable.jpg', 'T-219', 4, 'available', 980.00, 930.00, 'first floor', 'Modern table with USB charging ports', '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(40, 'images/tables/demoTable.jpg', 'T-220', 10, 'available', 2100.00, 2000.00, 'first floor', 'Celebration table with decoration service', '2025-12-07 08:32:20', '2025-12-07 08:32:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','staff','customer') DEFAULT 'customer',
  `status` enum('pending','verified','suspended') DEFAULT 'pending',
  `salary` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `contact`, `password`, `profile_pic`, `address`, `role`, `status`, `salary`, `created_at`, `updated_at`) VALUES
(1, 'Ramesh', 'Sharma', 'admin@hotelannapurna.com', '9841234567', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoAdmin.jpg', 'Kathmandu, Nepal', 'admin', 'verified', 50000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(2, 'Sita', 'Rai', 'sita.manager@hotelannapurna.com', '9841234568', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoAdmin.jpg', 'Kathmandu, Nepal', 'admin', 'verified', 45000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(3, 'Mahendra', 'Mahara', 'mahendra.manager@hotelannapurna.com', '9841234569', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoAdmin.jpg', 'Kathmandu, Nepal', 'admin', 'verified', 48000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(4, 'Krishna', 'Thapa', 'krishna.chef@hotelannapurna.com', '9841234569', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 35000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(5, 'Gita', 'Gurung', 'gita.receptionist@hotelannapurna.com', '9841234570', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 28000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(6, 'Hari', 'Magar', 'hari.waiter@hotelannapurna.com', '9841234571', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 25000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(7, 'Laxmi', 'Tamang', 'laxmi.housekeeping@hotelannapurna.com', '9841234572', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 24000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(8, 'Bikash', 'Shrestha', 'bikash.kitchen@hotelannapurna.com', '9841234573', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 30000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(9, 'Sunita', 'Poudel', 'sunita.bartender@hotelannapurna.com', '9841234574', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 27000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(10, 'Sanjay', 'Lama', 'sanjay.security@hotelannapurna.com', '9841234587', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 26000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(11, 'Bishnu', 'Rai', 'bishnu.assistant@hotelannapurna.com', '9841234588', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 29000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(12, 'Rama', 'Bhattarai', 'rama.cleaner@hotelannapurna.com', '9841234589', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 22000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(13, 'Tej', 'Gurung', 'tej.cook@hotelannapurna.com', '9841234590', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoStaff.jpg', 'Kathmandu, Nepal', 'staff', 'verified', 32000.00, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(14, 'Rajesh', 'Kumar', 'rajesh.kumar@gmail.com', '9841234575', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(15, 'Priya', 'Singh', 'priya.singh@yahoo.com', '9841234576', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(16, 'Amit', 'Patel', 'amit.patel@hotmail.com', '9841234577', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(17, 'Anjali', 'Verma', 'anjali.verma@outlook.com', '9841234578', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(18, 'Suresh', 'Bahadur', 'suresh.bdr@gmail.com', '9841234579', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(19, 'Kavita', 'Adhikari', 'kavita.adhikari@gmail.com', '9841234580', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(20, 'Deepak', 'Rana', 'deepak.rana@yahoo.com', '9841234581', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'pending', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(21, 'Manisha', 'Karki', 'manisha.karki@gmail.com', '9841234582', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(22, 'Prakash', 'Limbu', 'prakash.limbu@hotmail.com', '9841234583', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(23, 'Ritu', 'Chaudhary', 'ritu.chaudhary@gmail.com', '9841234584', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'pending', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(24, 'Nabin', 'Bhandari', 'nabin.bhandari@yahoo.com', '9841234585', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(25, 'Pooja', 'Thakur', 'pooja.thakur@outlook.com', '9841234586', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(26, 'Anita', 'Shrestha', 'anita.shrestha@gmail.com', '9841234591', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(27, 'Ramesh', 'KC', 'ramesh.kc@yahoo.com', '9841234592', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(28, 'Sabina', 'Maharjan', 'sabina.maharjan@gmail.com', '9841234593', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(29, 'Anil', 'Thapa', 'anil.thapa@hotmail.com', '9841234594', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'pending', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(30, 'Maya', 'Gurung', 'maya.gurung@outlook.com', '9841234595', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(31, 'Santosh', 'Pradhan', 'santosh.pradhan@gmail.com', '9841234596', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(32, 'Puja', 'Karki', 'puja.karki@yahoo.com', '9841234597', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(33, 'Dinesh', 'Basnet', 'dinesh.basnet@gmail.com', '9841234598', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(34, 'Sarita', 'Malla', 'sarita.malla@hotmail.com', '9841234599', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'pending', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(35, 'Bibek', 'Sharma', 'bibek.sharma@outlook.com', '9841234600', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:19', '2025-12-07 08:32:19'),
(36, 'Sushma', 'Rana', 'sushma.rana@gmail.com', '9841234601', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(37, 'Kamal', 'Shahi', 'kamal.shahi@yahoo.com', '9841234602', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(38, 'Nirmala', 'Thakuri', 'nirmala.thakuri@gmail.com', '9841234603', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(39, 'Uttam', 'Bohara', 'uttam.bohara@hotmail.com', '9841234604', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(40, 'Sapana', 'Dahal', 'sapana.dahal@outlook.com', '9841234605', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'pending', NULL, '2025-12-07 08:32:20', '2025-12-07 08:32:20'),
(41, 'Rajan', 'Adhikari', 'rajan.adhikari@gmail.com', '9841234606', '$2y$10$ZQZ/Bhejb0umb7lA1g8tOu1Ek2wkVAk54ukiAMsyShk1Ce5l/XxXW', 'images/profiles/demoUser.jpg', 'Kathmandu, Nepal', 'customer', 'verified', NULL, '2025-12-07 08:32:20', '2025-12-07 08:32:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`activity_type`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_author` (`author_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `blog_interactions`
--
ALTER TABLE `blog_interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_blog` (`blog_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`interaction_type`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`item_type`),
  ADD KEY `idx_item` (`item_id`);

--
-- Indexes for table `contact_requests`
--
ALTER TABLE `contact_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_code` (`code`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_dates` (`valid_from`,`valid_until`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_type` (`order_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_expiry` (`expiry`);

--
-- Indexes for table `registration_otps`
--
ALTER TABLE `registration_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_expiry` (`expiry`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_no` (`room_no`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_type` (`room_type`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `table_no` (`table_no`),
  ADD KEY `idx_status` (`booking_status`),
  ADD KEY `idx_location` (`location`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `blog_interactions`
--
ALTER TABLE `blog_interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_requests`
--
ALTER TABLE `contact_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registration_otps`
--
ALTER TABLE `registration_otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_interactions`
--
ALTER TABLE `blog_interactions`
  ADD CONSTRAINT `blog_interactions_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_interactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
