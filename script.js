// LuxReads JavaScript

// These variables are set by index.php based on PHP session data.
// They determine user's login and premium status on the client-side.
// Example: let userLoggedIn = false;
// Example: let userIsPremium = false;

const books = {
    business: [
        {
            title: "Atomic Habits",
            author: "James Clear",
            description: "A proven framework for building good habits and breaking bad ones.",
            image: "images/atomic.jpg",
            link: "download.php?file=" + encodeURIComponent("Atomic habits.pdf"),
            premium: true
        },
       
        {
            title: "Zero to One",
            author: "Peter Thiel",
            description: "Notes on startups, or how to build the future.",
            image: "images/zero to one.jpg",
            link: "download.php?file=" + encodeURIComponent("021.pdf"),
            premium: true
        },
        {
            title: "Start with Why",
            author: "Simon Sinek",
            description: "How great leaders inspire everyone to take action.",
            image: "images/start with why.jpg",
            link: "download.php?file=" + encodeURIComponent("start-with-why.pdf"),
            premium: false
        },
        {
           title: "Built To Last",
            author: "Jim Collins",
            description: "Long-lasting companies, visionary leadership",
            image: "images/buit_to_last.jpg",
            link: "download.php?file=" + encodeURIComponent("built_to_last.pdf"),
            premium: true
        }
    ],
    mindset: [
        {
            title: "Mindset",
            author: "Carol Dweck",
            description: "The new psychology of success.",
            image: "images/mindset.jpg",
            link: "download.php?file=" + encodeURIComponent("Mindset.pdf"),
            premium: false
        },
        
        {
            title: "Grit",
            author: "Angela Duckworth",
            description: "The power of passion and perseverance.",
            image: "images/grit.jpg",
            link: "download.php?file=" + encodeURIComponent("grit.pdf"),
            premium: false
        },
        {
            title: "The Magic of Thinking Big",
            author: "David J. Schwartz",
            description: "Acquire the secrets of success.",
            image: "images/The Magic of Thinking Big.jpg",
            link: "download.php?file=" + encodeURIComponent("the magic of thinking big.pdf"),
            premium: true
        }
    ],
    finance: [
        {
            title: "Rich Dad Poor Dad",
            author: "Robert T. Kiyosaki",
            description: "What the rich teach their kids about money.",
            image: "images/rich dad poor dad.jpg",
            link: "download.php?file=" + encodeURIComponent("Rich Dad Poor Dad.pdf"), // Matches PHP filename case
            premium: true
        },
        {
            title: "The Intelligent Investor",
            author: "Benjamin Graham",
            description: "The definitive book on value investing.",
            image: "images/the intelligent.jpg",
            link: "download.php?file=" + encodeURIComponent("the-intelligent-investor.pdf"),
            premium: true
        },
        {
            title: "The Psychology Of Money",
            author: "Morgan Housel",
            description: "Wealth mindset, behavior over theory, timeless lessons",
            image: "images/the_psycology of money.jpg",
            link: "download.php?file=" + encodeURIComponent("the-psychology-of-money.pdf"),
            premium: false
        },
        
        {
            title: "The Millionarie Fastlane",
            author: "MJ DeMarco",
            description: "Fast-track to wealth, entrepreneurship vs. slow-lane life",
            image: "images/the_millionaire_fastlane.jpg",
            link: "download.php?file=" + encodeURIComponent("the-millionaire-fastlane.pdf"),
            premium: false
        }
    ],
    self_help: [
        {
            title: "The Power of Now",
            author: "Eckhart Tolle",
            description: "Living in the present moment.",
            image: "images/the power of now.jpg",
            link: "download.php?file=" + encodeURIComponent("The Power Of Now.pdf"),
            premium: true
        },
        
        {
            title: "How to Win Friends and Influence People",
            author: "Dale Carnegie",
            description: "Timeless principles of communication.",
            image: "images/How to Win Friends and Influence People.jpg",
            link: "download.php?file=" + encodeURIComponent("how-to-win-friends-and-influence-people.pdf"),
            premium: true
        },
        {
            title: "The Four Agreements",
            author: "Don Miguel Ruiz",
            description: "A practical guide to personal freedom.",
            image: "images/four.jpg",
            link: "download.php?file=" + encodeURIComponent("the-four-agreements.pdf"),
            premium: false
        },
        {
            title: "You Are a Badass",
            author: "Jen Sincero",
            description: "How to stop doubting your greatness.",
            image: "images/you badass.jpg",
            link: "download.php?file=" + encodeURIComponent("you-are-a-badass.pdf"),
            premium: true
        }
    ],
    technology: [
        {
            title: "The Psychology of Persuasion",
            author: "Robert Cialdini",
            description: "Behavioral psychology, persuasion in business and life",
            image: "images/the_psychology_of_persuasion.jpg",
            link: "download.php?file=" + encodeURIComponent("The Psychology of Persuasion.pdf"),
            premium: true
        }
    ]
};

// Function to render books based on category and search query
function renderBooks(category = null, searchQuery = '') {
    const section = document.getElementById("book-section");
    section.innerHTML = ''; // Clear previous books

    let booksToRender = [];

    if (category && books[category]) {
        booksToRender = books[category];
    } else {
        // Combine all categories into one array if no specific category or category not found
        Object.values(books).forEach(categoryBooks => {
            booksToRender = booksToRender.concat(categoryBooks);
        });
    }

    // Filter by search query if provided
    const lowerCaseSearchQuery = searchQuery.toLowerCase();
    const filteredBooks = booksToRender.filter(book =>
        book.title.toLowerCase().includes(lowerCaseSearchQuery) ||
        book.author.toLowerCase().includes(lowerCaseSearchQuery) ||
        book.description.toLowerCase().includes(lowerCaseSearchQuery)
    );

    if (filteredBooks.length === 0) {
        section.innerHTML = '<p class="no-books-message">No books found matching your search criteria.</p>';
        return;
    }

    filteredBooks.forEach(book => {
        const card = document.createElement('div');
        card.className = 'book-card';

        let buttonHTML;
        // Determine button text and behavior based on book's premium status and user's premium status
        if (book.premium) {
            if (userIsPremium) {
                // If user is premium, allow direct download for premium books
                buttonHTML = `<a href="${book.link}" download class="download-btn">Download</a>`;
            } else {
                // If not premium, show "Premium 👑" button that triggers handleDownload
                buttonHTML = `<button onclick="handleDownload('${book.link}', true)" class="premium-btn">Premium 👑</button>`;
            }
        } else {
            // For free books, if user is logged in, allow direct download
            // If not logged in, trigger handleDownload (which will then show login modal)
            buttonHTML = `<button onclick="handleDownload('${book.link}', false)" class="download-btn">Download</button>`;
        }

        card.innerHTML = `
            <img src="${book.image}" alt="${book.title}" />
            <div class="details">
                <h3>${book.title}</h3>
                <p><strong>Author:</strong> ${book.author}</p>
                <p>${book.description}</p>
                ${buttonHTML}
            </div>
        `;

        section.appendChild(card);
    });
}

// Global variable for the currently active category filter
let currentFilterCategory = null;

// Filter function called on genre click
function filterBooks(category) {
    currentFilterCategory = category; // Update the current filter
    renderBooks(category, document.getElementById('searchInput').value);
}

// Show all books
function displayAllBooks() {
    currentFilterCategory = null; // Clear the current filter
    renderBooks(null, document.getElementById('searchInput').value);
}

// --- Modal Functions ---
function showAuthModal() {
    document.getElementById('auth-modal').style.display = 'flex';
}

function closeAuthModal() {
    document.getElementById('auth-modal').style.display = 'none';
}

function showPremiumModal() {
    const premiumModal = document.getElementById('premium-modal');
    if (premiumModal) {
        premiumModal.style.display = 'flex';
    } else {
        // Fallback or direct redirect if modal element is missing
        alert("You need a premium subscription to access this book. Please subscribe!");
        window.location.href = 'subscribe.php';
    }
}

function closePremiumModal() {
    const premiumModal = document.getElementById('premium-modal');
    if (premiumModal) {
        premiumModal.style.display = 'none';
    }
}

// --- Universal Download Handler ---
// This function is called when ANY download button is clicked.
function handleDownload(bookLink, isPremiumBook) {
    if (!userLoggedIn) {
        showAuthModal(); // User not logged in, show login/signup modal
        return;
    }

    if (isPremiumBook && !userIsPremium) {
        showPremiumModal(); // User is logged in but not premium, show subscription modal
        return;
    }

    // If we reach here, the user is authorized by client-side checks.
    // Redirect to download.php. The server will perform final checks.
    window.location.href = bookLink;
}

// --- Search Functionality ---
document.addEventListener('DOMContentLoaded', () => {
    // Initial render of all books
    renderBooks();

    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', (event) => {
            renderBooks(currentFilterCategory, event.target.value);
        });
    }

    // --- Handle redirects from download.php ---
    // Check URL parameters for access messages from download.php
    const urlParams = new URLSearchParams(window.location.search);
    const accessStatus = urlParams.get('access');

    if (accessStatus === 'login_required') {
        showAuthModal();
        // Clean URL after showing modal to avoid re-triggering on refresh
        history.replaceState({}, document.title, window.location.pathname);
    } else if (accessStatus === 'premium_required') {
        showPremiumModal();
        // Clean URL after showing modal
        history.replaceState({}, document.title, window.location.pathname);
    }
    // Also check for general errors from download.php (e.g., file not found, db error)
    const errorStatus = urlParams.get('error');
    if (errorStatus) {
        let errorMessage = "An unexpected error occurred.";
        if (errorStatus === 'file_not_found') {
            errorMessage = "The book you tried to download was not found.";
        } else if (errorStatus === 'db_connection' || errorStatus === 'db_query_failed') {
            errorMessage = "There was a problem connecting to the database. Please try again later.";
        } else if (errorStatus === 'no_file_specified') {
            errorMessage = "No book was specified for download.";
        }
        alert("Error: " + errorMessage); // Simple alert for general errors
        history.replaceState({}, document.title, window.location.pathname);
    }
});
