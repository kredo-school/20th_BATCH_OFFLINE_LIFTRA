@auth
    @if(!Auth::user()->has_completed_tour)
    <div id="app-tour-overlay"></div>
    <div id="app-tour-spotlight"></div>
    <div id="app-tour-tooltip">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="tour-step-indicator">Step <span id="tour-current-step">1</span> of 5</span>
            <button class="tour-btn-skip" onclick="terminateTour()">Skip Tutorial</button>
        </div>
        <h5 id="tour-title">Welcome to Liftra!</h5>
        <p id="tour-description">Let's take a quick tour to help you get started with your new life planning assistant.</p>
        <div class="d-flex justify-content-end mt-4">
            <button class="tour-btn-next" onclick="nextTourStep()">Next</button>
        </div>
    </div>

    <script>
    const tourSteps = [
        {
            title: "Welcome to Liftra!",
            description: "I am J.A.R.V.I.S., your personal life assistant. Let me show you how to navigate your new command center, Sir.",
            target: null, // Center of screen
            position: 'center'
        },
        {
            title: "The Sidebar",
            description: "This is your primary navigation. From here you can access your LifePlan, Calendar, Tasks, and more.",
            target: ".sidebar",
            position: 'right'
        },
        {
            title: "LifePlan & Categories",
            description: "This is the heart of Liftra. Organize your life into categories and set ambitious goals for your future.",
            target: ".nav-item-custom.active",
            position: 'right'
        },
        {
            title: "J.A.R.V.I.S. Assistant",
            description: "Need help? Just talk to me. Click the chat icon to ask me to create categories, goals, or tasks for you.",
            target: ".ai-chat-toggle",
            position: 'left'
        },
        {
            title: "Settings & Profile",
            description: "Customize your experience and manage your personal data here.",
            target: ".user-profile-card",
            position: 'top'
        }
    ];

    let currentStepIndex = 0;

    function startTour() {
        document.getElementById('app-tour-overlay').style.display = 'block';
        document.getElementById('app-tour-tooltip').style.display = 'block';
        showStep(0);
    }

    function showStep(index) {
        const step = tourSteps[index];
        const tooltip = document.getElementById('app-tour-tooltip');
        const spotlight = document.getElementById('app-tour-spotlight');
        
        document.getElementById('tour-title').innerText = step.title;
        document.getElementById('tour-description').innerText = step.description;
        document.getElementById('tour-current-step').innerText = index + 1;

        if (step.target) {
            const el = document.querySelector(step.target);
            if (el) {
                const rect = el.getBoundingClientRect();
                updateSpotlight(rect);
                positionTooltip(rect, step.position);
            } else {
                // If target not found on this page, skip to next
                nextTourStep();
            }
        } else {
            // Center screen
            spotlight.style.clipPath = 'none';
            tooltip.style.top = '50%';
            tooltip.style.left = '50%';
            tooltip.style.transform = 'translate(-50%, -50%)';
            tooltip.className = '';
        }
    }

    function updateSpotlight(rect) {
        const padding = 10;
        const top = rect.top - padding;
        const left = rect.left - padding;
        const bottom = rect.bottom + padding;
        const right = rect.right + padding;
        
        // Spotlight effect using polygon: outer box then inner cutout
        document.getElementById('app-tour-spotlight').style.clipPath = `polygon(
            0% 0%, 0% 100%, 100% 100%, 100% 0%, 0% 0%, 
            ${left}px ${top}px, ${right}px ${top}px, ${right}px ${bottom}px, ${left}px ${bottom}px, ${left}px ${top}px
        )`;
    }

    function positionTooltip(rect, position) {
        const tooltip = document.getElementById('app-tour-tooltip');
        tooltip.style.transform = 'none';
        tooltip.className = '';
        const padding = 20;

        if (position === 'right') {
            tooltip.style.top = rect.top + 'px';
            tooltip.style.left = (rect.right + padding) + 'px';
            tooltip.classList.add('tour-arrow-left');
        } else if (position === 'left') {
            tooltip.style.top = rect.top + 'px';
            tooltip.style.left = (rect.left - tooltip.offsetWidth - padding) + 'px';
            tooltip.classList.add('tour-arrow-right');
        } else if (position === 'top') {
            tooltip.style.top = (rect.top - tooltip.offsetHeight - padding) + 'px';
            tooltip.style.left = rect.left + 'px';
            tooltip.classList.add('tour-arrow-bottom');
        } else if (position === 'bottom') {
            tooltip.style.top = (rect.bottom + padding) + 'px';
            tooltip.style.left = rect.left + 'px';
            tooltip.classList.add('tour-arrow-top');
        }
    }

    function nextTourStep() {
        currentStepIndex++;
        if (currentStepIndex < tourSteps.length) {
            showStep(currentStepIndex);
        } else {
            terminateTour();
        }
    }

    async function terminateTour() {
        document.getElementById('app-tour-overlay').style.display = 'none';
        document.getElementById('app-tour-spotlight').style.display = 'none';
        document.getElementById('app-tour-tooltip').style.display = 'none';

        try {
            await fetch("{{ route('tour.complete') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
        } catch (e) { console.error("Tour completion sync failed", e); }
    }

    window.addEventListener('load', () => {
        setTimeout(startTour, 1000);
    });
    </script>
    @endif
@endauth
