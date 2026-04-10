@extends('layouts.app')

@push('styles')
<style>
    .settings-container {
        max-width: 800px;
        margin: 0 auto;
    }

    .help-header {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        padding: 40px 0 90px;
        border-radius: 0 0 24px 24px;
        text-align: center;
        margin-bottom: -40px;
    }

    .help-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
        border: none;
        margin-bottom: 2rem;
        overflow: hidden;
    }

    /* Accordion Customization */
    .accordion-item {
        border-left: none;
        border-right: none;
        border-top: none;
        border-bottom: 1px solid #f1f5f9;
        background-color: transparent;
    }

    .accordion-item:last-child {
        border-bottom: none;
    }

    .accordion-button {
        background-color: white !important; /* Keep it white even when expanded */
        color: #334155 !important;
        font-weight: 500;
        padding: 1.25rem 1.5rem;
        box-shadow: none !important;
        font-size: 0.95rem;
    }
    
    .accordion-button::after {
        background-size: 0.75rem;
    }

    .accordion-button:not(.collapsed)::after {
        filter: brightness(0) saturate(100%) invert(32%) sepia(50%) saturate(704%) hue-rotate(204deg) brightness(85%) contrast(87%); /* matching #475569 somewhat */
    }

    .accordion-icon {
        color: #64748b;
        width: 20px;
        margin-right: 12px;
        text-align: center;
    }

    .accordion-body {
        padding: 0 1.5rem 1.25rem 3.5rem; /* Indent body to align with text */
        color: #475569;
        font-size: 0.9rem;
    }

    .accordion-body ul {
        padding-left: 1.25rem;
        margin-bottom: 0;
    }
    
    .accordion-body li {
        margin-bottom: 0.5rem;
    }
    
    .accordion-body li:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@section('content')

<!-- Header (Matching the requested style, adjusted for Help) -->
<div class="page-header shadow-sm mt-0 mx-0 w-100" style="padding-top:20px; padding-bottom: 20px;">
    <div class="container-fluid px-2 px-md-4">
        <div class="d-flex align-items-center">
            
            <a href="{{ route('settings.index') }}" class="text-white text-decoration-none me-3 ms-2">
                <i class="fa-solid fa-chevron-left fs-5"></i>
            </a>
            
            <div>
                <h3 class="mb-0 fw-bold">About Liftra</h3>
                <p class="mb-0 small text-white">Help & Support Center</p>
            </div>
        </div>
    </div>
</div>

<div class="container settings-container pb-5" {{--style="margin-top: -30px; position:relative; z-index: 10;"--}}>
    
    <!-- BOX 1: Guides and Best Practices -->
    <div class="card help-card">
        <div class="accordion" id="accordionGuides">
            
            <!-- Getting Started -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingGettingStarted">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGettingStarted" aria-expanded="false" aria-controls="collapseGettingStarted">
                        <i class="fa-solid fa-rocket accordion-icon"></i>
                        {{ __('Getting Started') }}
                    </button>
                </h2>
                <div id="collapseGettingStarted" class="accordion-collapse collapse" aria-labelledby="headingGettingStarted" data-bs-parent="#accordionGuides">
                    <div class="accordion-body">
                        <strong>For Beginners</strong>
                        <ul class="mt-2">
                            <li>First 10 Minutes Setup</li>
                            <li>Initial Setup Guide</li>
                            <li>Creating Your First Goal</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Feature Guides -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFeatures">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFeatures" aria-expanded="false" aria-controls="collapseFeatures">
                        <i class="fa-solid fa-book-open accordion-icon"></i>
                        {{ __('Feature Guides') }}
                    </button>
                </h2>
                <div id="collapseFeatures" class="accordion-collapse collapse" aria-labelledby="headingFeatures" data-bs-parent="#accordionGuides">
                    <div class="accordion-body">
                        <strong>How to use functions</strong>
                        <ul class="mt-2">
                            <li>LifePlan</li>
                            <li>Calendar</li>
                            <li>Task</li>
                            <li>Habit</li>
                            <li>Journal</li>
                            <li>Professional Profile</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Best Practices -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingBestPractices">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBestPractices" aria-expanded="false" aria-controls="collapseBestPractices">
                        <i class="fa-solid fa-lightbulb accordion-icon"></i>
                        {{ __('Best Practices') }}
                    </button>
                </h2>
                <div id="collapseBestPractices" class="accordion-collapse collapse" aria-labelledby="headingBestPractices" data-bs-parent="#accordionGuides">
                    <div class="accordion-body">
                        <strong>Tips for Success</strong>
                        <ul class="mt-2">
                            <li>Tips for Consistency</li>
                            <li>Goal Setting Tips</li>
                            <li>Habit Building Techniques</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- BOX 2: Support and Troubleshooting -->
    <div class="card help-card">
        <div class="accordion" id="accordionSupport">
            
            <!-- Troubleshooting -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTrouble">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTrouble" aria-expanded="false" aria-controls="collapseTrouble">
                        <i class="fa-solid fa-wrench accordion-icon"></i>
                        {{ __('Troubleshooting') }}
                    </button>
                </h2>
                <div id="collapseTrouble" class="accordion-collapse collapse" aria-labelledby="headingTrouble" data-bs-parent="#accordionSupport">
                    <div class="accordion-body">
                        <strong>Resolving Issues</strong>
                        <ul class="mt-2">
                            <li>Login Issues</li>
                            <li>Syncing Data</li>
                            <li>Display Glitches</li>
                            <li>Notifications</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- FAQ -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingFAQ">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFAQ" aria-expanded="false" aria-controls="collapseFAQ">
                        <i class="fa-solid fa-circle-question accordion-icon"></i>
                        {{ __('FAQ') }}
                    </button>
                </h2>
                <div id="collapseFAQ" class="accordion-collapse collapse" aria-labelledby="headingFAQ" data-bs-parent="#accordionSupport">
                    <div class="accordion-body">
                        <strong>Frequently Asked Questions</strong>
                        <p class="mt-2 text-muted mb-0">Answers to common questions will be listed here.</p>
                    </div>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingContact">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContact" aria-expanded="false" aria-controls="collapseContact">
                        <i class="fa-solid fa-headset accordion-icon"></i>
                        {{ __('Contact Support') }}
                    </button>
                </h2>
                <div id="collapseContact" class="accordion-collapse collapse" aria-labelledby="headingContact" data-bs-parent="#accordionSupport">
                    <div class="accordion-body">
                        <strong>Inquiries</strong>
                        <p class="mt-2 text-muted mb-0">If you need further assistance, please reach out to our support team.</p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
