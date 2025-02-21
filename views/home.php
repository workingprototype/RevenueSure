<?php
$appName = $_ENV['APP_NAME'] ?? 'RevenueSure';  // Fallback if not set
?>

<!-- Hero Section -->
<div class="container mx-auto mt-10 px-4 fade-in">
  <div class="bg-gradient-to-r from-gray-100 to-white p-12 shadow-lg rounded-lg text-center transition-all duration-500 ease-in-out transform hover:scale-105">
    <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
      <i class="fas fa-rocket mr-2 text-blue-500"></i> Welcome to <?php echo htmlspecialchars($appName); ?>
    </h1>
    <p class="text-lg text-gray-700 mb-8">
      Revolutionize your business growth with <?php echo htmlspecialchars($appName); ?> – the all-in-one, AI-powered CRM that seamlessly manages leads, connects you with customers, and optimizes revenue. Experience the future of intelligent business.
    </p>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="<?php echo BASE_URL; ?>leads/search" class="bg-blue-600 text-white px-8 py-4 rounded-lg shadow transition-all duration-500 hover:bg-blue-700">
        <i class="fas fa-search mr-2"></i> Explore Leads Now
      </a>
    <?php else: ?>
      <a href="<?php echo BASE_URL; ?>auth/register" class="bg-blue-600 text-white px-8 py-4 rounded-lg transition-all duration-500 hover:bg-blue-700">
        <i class="fas fa-user-plus mr-2"></i> Start Your Free Trial
      </a>
    <?php endif; ?>
  </div>
</div>

<!-- Key Benefits Section -->
<div class="container mx-auto mt-16 px-4 fade-in">
  <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">
    <i class="fas fa-key mr-2 text-blue-500"></i> Unlock Your Business Potential with AI
  </h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Benefit 1 -->
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-bullseye mr-2 text-green-500"></i> Target Qualified Leads 
        <span class="badge bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Verified</span>
      </h3>
      <p class="text-gray-600">
        Our AI analyzes customer behavior to pinpoint high-quality leads.
        <br><em>Example:</em> It identifies prospects with a 20% higher conversion probability based on browsing patterns.
      </p>
    </div>
    <!-- Benefit 2 -->
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-handshake mr-2 text-purple-500"></i> Strengthen Customer Relationships 
        <span class="badge bg-purple-200 text-purple-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Loyalty</span>
      </h3>
      <p class="text-gray-600">
        AI-driven insights personalize every interaction, ensuring lasting relationships.
        <br><em>Example:</em> Natural language processing categorizes emails and suggests tailored follow-ups.
      </p>
    </div>
    <!-- Benefit 3 -->
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-money-bill-wave mr-2 text-yellow-500"></i> Maximize Revenue Streams 
        <span class="badge bg-yellow-200 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Profitability</span>
      </h3>
      <p class="text-gray-600">
        AI forecasts trends and uncovers hidden opportunities in your sales data.
        <br><em>Example:</em> It spots underperforming segments and recommends targeted promotions.
      </p>
    </div>
  </div>
</div>

<!-- CRM Feature Highlights Section -->
<div class="container mx-auto mt-16 px-4 fade-in">
  <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">
    <i class="fas fa-cogs mr-2 text-blue-500"></i> Key CRM Features Powered by AI
  </h2>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
    <!-- Feature 1: Lead Management -->
    <div class="bg-gray-50 p-8 text-center shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-address-book mr-2 text-red-500"></i> Lead Management 
        <span class="status-tag bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Efficient</span>
      </h3>
      <p class="text-gray-600">
        AI scores and prioritizes leads to ensure you focus on the most promising prospects.
        <br><em>Example:</em> It flags leads likely to convert and automatically schedules follow-up reminders.
      </p>
    </div>
    <!-- Feature 2: Contact Management -->
    <div class="bg-gray-50 p-8 text-center shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-users mr-2 text-orange-500"></i> Contact Management 
        <span class="status-tag bg-orange-100 text-orange-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Centralized</span>
      </h3>
      <p class="text-gray-600">
        AI deduplicates and organizes your contacts for seamless communication.
        <br><em>Example:</em> It clusters similar contacts and automates segmentation for targeted campaigns.
      </p>
    </div>
    <!-- Feature 3: Sales Pipeline -->
    <div class="bg-gray-50 p-8 text-center shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-funnel-dollar mr-2 text-yellow-500"></i> Sales Pipeline 
        <span class="status-tag bg-yellow-100 text-yellow-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Visualized</span>
      </h3>
      <p class="text-gray-600">
        AI analyzes your pipeline data to forecast outcomes and optimize processes.
        <br><em>Example:</em> It predicts stalled deals and suggests corrective actions.
      </p>
    </div>
    <!-- Feature 4: Reporting & Analytics -->
    <div class="bg-gray-50 p-8 text-center shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-chart-bar mr-2 text-green-500"></i> Reporting & Analytics 
        <span class="status-tag bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Data-Driven</span>
      </h3>
      <p class="text-gray-600">
        AI generates dynamic, real-time reports to keep you informed.
        <br><em>Example:</em> It detects anomalies in sales data and suggests strategic adjustments.
      </p>
    </div>
    <!-- Feature 5: Email Integration -->
    <div class="bg-gray-50 p-8 text-center shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-envelope mr-2 text-teal-500"></i> Email Integration 
        <span class="status-tag bg-teal-100 text-teal-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Seamless</span>
      </h3>
      <p class="text-gray-600">
        AI optimizes email timing and content to maximize customer engagement.
        <br><em>Example:</em> It schedules emails when your audience is most active and personalizes subject lines based on past interactions.
      </p>
    </div>
    <!-- Feature 6: Credit Management -->
    <div class="bg-gray-50 p-8 text-center shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-coins mr-2 text-blue-500"></i> Credit Management 
        <span class="status-tag bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Cost-Effective</span>
      </h3>
      <p class="text-gray-600">
        AI monitors credit usage to optimize your feature allocation.
        <br><em>Example:</em> It analyzes spending patterns and predicts future credit needs.
      </p>
    </div>
    <!-- Feature 7: Task Management -->
    <div class="bg-gray-50 p-8 text-center shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-tasks mr-2 text-indigo-500"></i> Task Management 
        <span class="status-tag bg-indigo-100 text-indigo-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">Organized</span>
      </h3>
      <p class="text-gray-600">
        AI prioritizes tasks and automates assignments to boost productivity.
        <br><em>Example:</em> It reassigns overdue tasks based on team availability and urgency.
      </p>
    </div>
    <!-- Feature 8: Mobile Access -->
    <div class="bg-gray-50 p-8 text-center shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">
        <i class="fas fa-mobile-alt mr-2 text-purple-500"></i> Mobile Access 
        <span class="status-tag bg-purple-100 text-purple-800 py-1 px-3 rounded-full text-xs font-semibold ml-2">On-the-Go</span>
      </h3>
      <p class="text-gray-600">
        AI customizes your mobile dashboard for quick, intuitive access.
        <br><em>Example:</em> It highlights frequently used features and suggests shortcuts.
      </p>
    </div>
  </div>
</div>

<!-- How It Works Section -->
<div class="container mx-auto mt-16 px-4 fade-in">
  <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">
    <i class="fas fa-cogs mr-2 text-blue-500"></i> How It Works with AI
  </h2>
  <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">1. Sign Up</h3>
      <p class="text-gray-600">Register and let our AI instantly learn your business needs.</p>
    </div>
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">2. Integrate</h3>
      <p class="text-gray-600">Connect your tools and allow AI to merge your data sources seamlessly.</p>
    </div>
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">3. Manage</h3>
      <p class="text-gray-600">AI organizes your leads, contacts, and tasks, automating routine work.</p>
    </div>
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 transform hover:scale-105">
      <h3 class="text-xl font-semibold text-gray-900 mb-4">4. Grow</h3>
      <p class="text-gray-600">Watch your business expand as AI continuously optimizes performance.</p>
    </div>
  </div>
</div>

<!-- Pricing Plans Section -->
<div class="container mx-auto mt-16 px-4 fade-in">
  <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">
    <i class="fas fa-tags mr-2 text-blue-500"></i> Pricing Plans
  </h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Plan 1: Free -->
    <div class="bg-white p-8 shadow rounded-lg text-center transition-all duration-500 transform hover:scale-105">
      <h3 class="text-2xl font-bold text-gray-900 mb-4">Free</h3>
      <p class="text-lg font-bold text-gray-900 mb-4">$0/month</p>
      <ul class="text-gray-600 mb-4">
        <li>Basic AI-powered CRM features</li>
        <li>Up to 100 leads with AI scoring</li>
        <li>Email support</li>
      </ul>
      <a href="<?php echo BASE_URL; ?>auth/register" class="bg-blue-600 text-white px-8 py-4 rounded-lg transition-all duration-500 hover:bg-blue-700">
        Get Started
      </a>
    </div>
    <!-- Plan 2: Pro -->
    <div class="bg-white p-8 shadow rounded-lg text-center transition-all duration-500 transform hover:scale-105">
      <h3 class="text-2xl font-bold text-gray-900 mb-4">Pro</h3>
      <p class="text-lg font-bold text-gray-900 mb-4">$49/month</p>
      <ul class="text-gray-600 mb-4">
        <li>Advanced AI analytics & features</li>
        <li>Unlimited leads with AI prioritization</li>
        <li>Priority AI support</li>
      </ul>
      <a href="<?php echo BASE_URL; ?>auth/register" class="bg-blue-600 text-white px-8 py-4 rounded-lg transition-all duration-500 hover:bg-blue-700">
        Get Started
      </a>
    </div>
    <!-- Plan 3: Enterprise -->
    <div class="bg-white p-8 shadow rounded-lg text-center transition-all duration-500 transform hover:scale-105">
      <h3 class="text-2xl font-bold text-gray-900 mb-4">Enterprise</h3>
      <p class="text-lg font-bold text-gray-900 mb-4">Contact Us</p>
      <ul class="text-gray-600 mb-4">
        <li>Custom AI solutions</li>
        <li>Dedicated AI support & integration</li>
        <li>Personalized onboarding assistance</li>
      </ul>
      <a href="<?php echo BASE_URL; ?>contact" class="bg-blue-600 text-white px-8 py-4 rounded-lg transition-all duration-500 hover:bg-blue-700">
        Contact Sales
      </a>
    </div>
  </div>
</div>

<!-- FAQ Section -->
<div class="container mx-auto mt-16 px-4 fade-in">
  <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">
    <i class="fas fa-question-circle mr-2 text-blue-500"></i> Frequently Asked Questions
  </h2>
  <div class="space-y-8">
    <!-- FAQ 1 -->
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 hover:shadow-xl">
      <h3 class="text-xl font-bold text-gray-900 mb-2">What is <?php echo htmlspecialchars($appName); ?>?</h3>
      <p class="text-gray-600">
        <?php echo htmlspecialchars($appName); ?> is an AI-powered CRM designed to help you manage leads, contacts, and your sales pipeline while providing actionable insights.
      </p>
    </div>
    <!-- FAQ 2 -->
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 hover:shadow-xl">
      <h3 class="text-xl font-bold text-gray-900 mb-2">Is there a free trial available?</h3>
      <p class="text-gray-600">
        Yes – start with our free plan, experience our smart AI features, and upgrade as your business grows.
      </p>
    </div>
    <!-- FAQ 3 -->
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 hover:shadow-xl">
      <h3 class="text-xl font-bold text-gray-900 mb-2">How secure is my data?</h3>
      <p class="text-gray-600">
        Data security is paramount. Our AI continuously monitors and employs industry-standard encryption to protect your information.
      </p>
    </div>
  </div>
</div>

<!-- Call to Action Section -->
<div class="container mx-auto mt-16 px-4 fade-in">
  <div class="bg-blue-700 text-white p-12 shadow-lg rounded-lg text-center transition-all duration-500 hover:shadow-xl">
    <h2 class="text-3xl font-bold mb-8">
      <i class="fas fa-handshake mr-2"></i> Ready to Transform Your Business with AI?
    </h2>
    <p class="text-lg mb-8">
      Start your free trial today and experience the intelligent, AI-powered capabilities of <?php echo htmlspecialchars($appName); ?>.
    </p>
    <a href="<?php echo BASE_URL; ?>auth/register" class="bg-white text-blue-700 px-8 py-4 rounded-lg border transition-all duration-500 hover:bg-blue-50 font-semibold">
      <i class="fas fa-arrow-right mr-2"></i> Get Started Now
    </a>
  </div>
</div>

<!-- Testimonials Section -->
<div class="container mx-auto mt-16 px-4 fade-in">
  <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">
    <i class="fas fa-quote-left mr-2 text-blue-500"></i> What Our Customers Are Saying
  </h2>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Testimonial 1 -->
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 hover:shadow-xl">
      <p class="text-gray-700 italic mb-4">
        "<?php echo htmlspecialchars($appName); ?> has completely transformed our sales process! Its AI-powered simplicity cuts through the noise and delivers smart, measurable results."
      </p>
      <div class="flex items-center">
        <div class="ml-4">
          <p class="font-semibold text-gray-900">Jane Doe <span class="text-gray-600 text-sm">verified</span></p>
          <p class="text-gray-600">CEO, Acme Corp</p>
        </div>
      </div>
    </div>
    <!-- Testimonial 2 -->
    <div class="bg-white p-8 shadow rounded-lg transition-all duration-500 hover:shadow-xl">
      <p class="text-gray-700 italic mb-4">
        "The AI-driven approach of <?php echo htmlspecialchars($appName); ?> is unlike any other CRM. It’s smart, efficient, and a true game changer for our business."
      </p>
      <div class="flex items-center">
        <div class="ml-4">
          <p class="font-semibold text-gray-900">John Smith <span class="text-gray-600 text-sm">verified</span></p>
          <p class="text-gray-600">Sales Manager, Beta Industries</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Statistics Section -->
<div class="container mx-auto mt-16 px-4 fade-in">
  <h2 class="text-3xl font-bold text-center text-gray-800 mb-10">
    <i class="fas fa-chart-line mr-2 text-blue-500"></i> Powering Business Growth with AI
  </h2>
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Statistic 1 -->
    <div class="bg-white p-8 shadow rounded-lg text-center transition-all duration-500 hover:shadow-xl">
      <p class="text-5xl font-bold text-blue-600">
        <i class="fas fa-building mr-2"></i> 500+
      </p>
      <p class="text-gray-700 mt-4">Businesses Served</p>
    </div>
    <!-- Statistic 2 -->
    <div class="bg-white p-8 shadow rounded-lg text-center transition-all duration-500 hover:shadow-xl">
      <p class="text-5xl font-bold text-green-600">
        <i class="fas fa-arrow-up mr-2"></i> 30%
      </p>
      <p class="text-gray-700 mt-4">Average Sales Increase</p>
    </div>
    <!-- Statistic 3 -->
    <div class="bg-white p-8 shadow rounded-lg text-center transition-all duration-500 hover:shadow-xl">
      <p class="text-5xl font-bold text-purple-600">
        <i class="fas fa-headset mr-2"></i> 24/7
      </p>
      <p class="text-gray-700 mt-4">Dedicated Support</p>
    </div>
  </div>
</div>
