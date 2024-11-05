@extends('layout')

@section('page-title', 'Home')
@section('page-content')
<div class="home-container hompage">
    <div class="welcome mb-4 border-bottom">
        <div class="welcome-left">
            <div class="header-caption mb-3">
                <h1 class="header-text">Find Opportunity in UniqHire</h1>
            </div>
            <div class="sub-caption mb-4">
                <p>Welcome to Uniqhire, where every ability finds opportunity! Creating bridges to people with disabilities, fostering inclusivity and celebrating diverse talents. Join us in building a world where everyone thrives!</p>
            </div>
            <div class="">
                <button type="button" class="btn-outline">Explore</button>
            </div>
        </div>
        <div id="carouselExample" class="carousel slide carousel-container welcome-right">
            <div class="carousel-inner">
                @foreach ( $images as $index => $image )
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    <img src="{{ asset($image) }}" class="d-block w-100" alt="...">

                </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>
    <div class="mission-vision border-bottom">
        <div class="mission">
            <h2 class="mb-4">Mission</h2>
            <p>UniqHire aims to bridge the gap between PWD users, Training Agencies, Companies, and Sponsors by providing a user-friendly platform that simplifies transactions, enhances communication, and promotes equal opportunities for all stakeholders in a transparent and efficient manner.</p>
        </div>
        <div class="vision ">
            <h2 class="mb-4">Vision</h2>
            <p>To create an inclusive, accessible, and empowering platform where PWD users, Training Agencies, Companies, and Sponsors seamlessly connect and collaborate, fostering opportunities for growth, employment, and mutual support.</p>
        </div>
    </div>
    <div class="statistics mb-4 border-bottom">
        <div class="stats-1 d-flex align-items-center justify-content-center">
            <div class="d-flex flex-column justify-content-center text-center">
                <i class='bx bx-handicap count-index-icon'></i>
                <span class="user-count">
                    {{ $pwdCount }}
                    <p class="user-count">PWDs</p>
                </span>
            </div>
        </div>
        <div class="stats-2 d-flex align-items-center justify-content-center">
            <div class="d-flex flex-column justify-content-center text-center">
                <i class='bx bxs-school count-index-icon'></i>
                <span class="user-count">
                    {{ $trainerCount }}
                    <p class="user-count">Training Agencies</p>
                </span>
            </div>
        </div>
        <div class="stats-3 d-flex align-items-center justify-content-center">
            <div class="d-flex flex-column justify-content-center text-center">
                <i class='bx bx-briefcase-alt-2 count-index-icon'></i>
                <span class="user-count">
                    {{ $employerCount }}
                    <p class="user-count">Companies</p>
                </span>
            </div>
        </div>
        <div class="stats-4 d-flex align-items-center justify-content-center">
            <div class="d-flex flex-column justify-content-center text-center">
                <i class='bx bx-money-withdraw count-index-icon'></i>
                <span class="user-count">
                    {{ $sponsorCount }}
                    <p class="user-count">Sponsors</p>
                </span>
            </div>
        </div>
    </div>
    <div class="about mb-4" id="about">
        <div class="team">
            <img src="{{asset('/images/team.png')}}" alt="">
        </div>
        <div class="about-text">
            <h2 class="mb-4">About Us</h2>
            <p class="mb-4">UniqHire is dedicated to creating opportunities for individuals with disabilities. We believe in a world where everyone, regardless of their abilities, can thrive and contribute to the workforce. Our platform connects talented individuals with disabilities to training programs and job opportunities tailored to their unique skills and aspirations.</p>
        </div>
    </div>
    <div>

    </div>
</div>



@endsection

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    });
</script>