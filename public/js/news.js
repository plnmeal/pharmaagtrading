// public/js/news.js

document.addEventListener('DOMContentLoaded', () => {

    const newsGrid = document.getElementById('news-grid');
    if (!newsGrid) return; // Exit if not on the news page

    // --- DUMMY DATA (Will be replaced by backend data later) ---
    const allArticles = [
        { id: 1, title: 'Expanding Cold Chain Capabilities in the Cibao Region', image: 'https://via.placeholder.com/600x400.png?text=Hospital+Corridor', date: 'JUNE 27, 2025', category: 'HEALTHCARE LOGISTICS', snippet: 'We are proud to announce the expansion of our validated cold chain services, ensuring vaccine and biologic integrity...'},
        { id: 2, title: 'Navigating New DIGEMAPS Compliance Standards', image: 'https://via.placeholder.com/600x400.png?text=Pharmacist+with+Tablet', date: 'JUNE 15, 2025', category: 'REGULATORY UPDATES', snippet: 'Our expert team breaks down the latest regulatory updates from DIGEMAPS to help our partners stay ahead...'},
        { id: 3, title: 'PharmaAGTrading Implements AI for Route Optimization', image: 'https://via.placeholder.com/600x400.png?text=Warehouse+Technology', date: 'JUNE 5, 2025', category: 'INNOVATION', snippet: 'Discover how our new AI-powered logistics platform is reducing delivery times and improving efficiency across the DR...'},
        { id: 4, title: 'Annual Partnership Summit Highlights Supply Chain Resilience', image: 'https://via.placeholder.com/600x400.png?text=Conference+Room', date: 'MAY 28, 2025', category: 'COMPANY NEWS', snippet: 'A look back at the key takeaways from our successful 2025 Partnership Summit held in Santo Domingo...'},
        { id: 5, title: 'The Importance of Real-Time Temperature Monitoring', image: 'https://via.placeholder.com/600x400.png?text=Temperature+Graph', date: 'MAY 19, 2025', category: 'EXPERT INSIGHTS', snippet: 'Why granular, real-time data is non-negotiable for the modern pharmaceutical cold chain...'},
        { id: 6, title: 'PharmaAGTrading Awarded ISO 9001:2015 Certification', image: 'https://via.placeholder.com/600x400.png?text=ISO+Certificate', date: 'MAY 10, 2025', category: 'COMPANY NEWS', snippet: 'This certification marks a major milestone in our unwavering commitment to quality management and customer satisfaction...'},
        { id: 7, title: 'A Guide to Pharmaceutical Warehousing Best Practices', image: 'https://via.placeholder.com/600x400.png?text=Modern+Warehouse', date: 'APRIL 25, 2025', category: 'HEALTHCARE LOGISTICS', snippet: 'From inventory management to environmental controls, we explore the cornerstones of a world-class pharma warehouse...'},
        { id: 8, title: 'New Fleet of Eco-Friendly Delivery Vehicles Arrives', image: 'https://via.placeholder.com/600x400.png?text=Electric+Van', date: 'APRIL 18, 2025', category: 'INNOVATION', snippet: 'Our investment in sustainable logistics continues with the rollout of our new all-electric delivery fleet...'},
        { id: 9, title: 'Understanding the New Serialized-Tracking Mandates', image: 'https://via.placeholder.com/600x400.png?text=Barcode+Scanner', date: 'APRIL 02, 2025', category: 'REGULATORY UPDATES', snippet: 'Are you ready for the upcoming track-and-trace regulations? Our experts provide a clear, actionable guide...'},
        { id: 10, title: 'PharmaAGTrading Partners with Local NGO for Health Outreach', image: 'https://via.placeholder.com/600x400.png?text=Community+Health+Fair', date: 'MARCH 22, 2025', category: 'COMPANY NEWS', snippet: 'We are thrilled to support community health initiatives by providing logistical support and medical supplies...'},
        { id: 11, title: 'The Future of Pharma Distribution: A 2025 Outlook', image: 'https://via.placeholder.com/600x400.png?text=Futuristic+Globe', date: 'MARCH 14, 2025', category: 'EXPERT INSIGHTS', snippet: 'From AI to blockchain, we explore the technologies set to revolutionize our industry in the coming years...'},
        { id: 12, title: 'Ensuring Data Integrity in Pharmaceutical Logistics', image: 'https://via.placeholder.com/600x400.png?text=Secure+Data+Server', date: 'MARCH 01, 2025', category: 'INNOVATION', snippet: 'Learn about the measures we take to ensure all our logistical and client data is secure, compliant, and reliable...'},
    ];

    // --- STATE MANAGEMENT ---
    let currentPage = 0;
    const articlesPerPage = 6;
    let isLoading = false;

    // --- DOM ELEMENTS ---
    const loader = document.getElementById('loader');

    // --- CORE FUNCTIONS ---
    const createArticleCard = (article) => {
        const cardLink = document.createElement('a');
        cardLink.href = 'news-detail.html'; // Will update to Laravel route later: `/news/${article.id}`
        cardLink.className = 'news-card';

        cardLink.innerHTML = `
            <img src="${article.image}" alt="${article.title}" loading="lazy" decoding="async">
            <div class="news-card-content">
                <span class="news-meta">${article.category} • ${article.date}</span>
                <h3>${article.title}</h3>
                <p>${article.snippet}</p>
                <span class="read-more-btn">Read More &rarr;</span>
            </div>
        `;
        return cardLink;
    };

    const loadArticles = () => {
        if (isLoading) return;
        isLoading = true;
        loader.style.display = 'block';

        setTimeout(() => {
            const start = currentPage * articlesPerPage;
            const end = start + articlesPerPage;
            const articlesToLoad = allArticles.slice(start, end);

            if (articlesToLoad.length > 0) {
                articlesToLoad.forEach(article => {
                    newsGrid.appendChild(createArticleCard(article));
                });
                currentPage++;
            }

            isLoading = false;
            loader.style.display = 'none';
        }, 500);
    };

    // --- EVENT LISTENERS ---
    const newsPageObserver = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !isLoading) {
            const hasMoreArticles = currentPage * articlesPerPage < allArticles.length;
            if (hasMoreArticles) {
                loadArticles();
            }
        }
    }, {
        rootMargin: '0px 0px 400px 0px'
    });

    if (loader) {
        newsPageObserver.observe(loader);
    }

    // --- INITIALIZATION ---
    loadArticles(); // Load initial set of articles

});