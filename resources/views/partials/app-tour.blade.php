@auth
    @php
        $user = Auth::user();
        $path = request()->path();
        
        $tourType = 'home';
        if (str_contains($path, 'lifeplan/category/')) {
            $tourType = 'category';
        } elseif (str_contains($path, 'lifeplan/goal/') || str_contains($path, 'lifeplan/milestone/')) {
            $tourType = 'milestone';
        }

        $isCompleted = false;
        if ($tourType == 'home') $isCompleted = $user->tour_home_completed;
        if ($tourType == 'category') $isCompleted = $user->tour_category_completed;
        if ($tourType == 'milestone') $isCompleted = $user->tour_milestone_completed;

        $showTour = (!$isCompleted) || request()->has('forceTour');
    @endphp

    @if($showTour)
    <div id="app-tour-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9998;"></div>
    <div id="app-tour-spotlight" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: transparent; z-index: 9999; pointer-events: none;"></div>
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
    function getTourConfig() {
        const path = window.location.pathname;
        
        // --- PAGE: Category View ---
        if (path.includes('/lifeplan/category/') && document.querySelector('.timeline-container')) {
            return {
                type: 'category',
                steps: [
                    {
                        title: "Category Insights",
                        description: "Sir, this is your category deep-dive. Here you can see your long-term timeline and overall progress.",
                        target: ".card.shadow-sm.rounded-4.p-4.mb-5",
                        position: 'bottom'
                    },
                    {
                        title: "The Timeline",
                        description: "Your life goals are mapped across decades. It is quite a legacy you are building.",
                        target: ".timeline-container",
                        position: 'right'
                    },
                    {
                        title: "Add Goals",
                        description: "Should you have a new aspiration, you can add it directly to this category here.",
                        target: "[data-bs-target='#addGoalModal']",
                        position: 'left'
                    }
                ]
            };
        }

        // --- PAGE: Milestone/Goal View ---
        if (path.includes('/lifeplan/goal/') || path.includes('/lifeplan/milestone/') || (path.includes('/lifeplan/category/') && document.querySelector('.milestone-card'))) {
            return {
                type: 'milestone',
                steps: [
                    {
                        title: "Operational Details",
                        description: "We are now looking at the specific milestones for this goal. Precision is key, Sir.",
                        target: ".card-body.p-4.px-5", // Stats bar
                        position: 'bottom'
                    },
                    {
                        title: "Milestones",
                        description: "These are the checkpoints on your journey. Check them off as we conquer them.",
                        target: ".milestone-card",
                        position: 'right'
                    },
                    {
                        title: "Timeline History",
                        description: "Every action we take is recorded here in your personal history.",
                        target: "#timelineView",
                        position: 'left'
                    }
                ]
            };
        }

        // --- DEFAULT/HOME PAGE ---
        return {
            type: 'home',
            steps: [
                {
                    title: "Welcome to Liftra!",
                    description: "I am J.A.R.V.I.S., your personal life assistant. Let me show you how to navigate your new command center, Sir.",
                    target: null,
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
                }
            ]
        };
    }

    const tourConfig = getTourConfig();
    const tourSteps = tourConfig.steps;
    const tourType = tourConfig.type;
    let currentStepIndex = 0;

    function startTour() {
        console.log(`J.A.R.V.I.S. Tour: Commencing automatic scan for ${tourType}...`);
        
        setTimeout(() => {
            document.getElementById('app-tour-overlay').style.display = 'block';
            document.getElementById('app-tour-spotlight').style.display = 'block';
            document.getElementById('app-tour-tooltip').style.display = 'block';
            showStep(0);
        }, 1500);
    }

    // Remove unused waitForTarget logic
    /* 
    function waitForTarget() { ... } 
    */

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
                },
                body: JSON.stringify({ type: tourType })
            });
        } catch (e) { console.error("Tour completion sync failed", e); }
    }

    window.addEventListener('load', () => {
        setTimeout(startTour, 1000);
    });
    </script>
    @endif
@endauth
