@extends('layouts.app')

@section('title', 'Livraisons & Retours')

@section('content')
<div class="min-h-screen bg-background">
    <!-- Header -->
    <div class="relative py-16 bg-primary text-white">
        <div class="container mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-center">Livraisons & Retours</h1>
            <p class="text-xl text-center mt-4 opacity-90">Toutes les informations sur vos commandes</p>
        </div>
    </div>

    <!-- Content -->
    <div class="container mx-auto px-4 py-16">
        <!-- Actions buttons - Outside white container -->
        <div class="flex flex-col sm:flex-row gap-4 mb-8 justify-center">
            <a href="{{ asset('documents/LIVRAISONS ET RETOURS ASTROLAB.pdf') }}" 
               target="_blank" 
               class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Ouvrir le PDF
            </a>
            <a href="{{ asset('documents/LIVRAISONS ET RETOURS ASTROLAB.pdf') }}" 
               download="Livraisons-Retours-Astrolab.pdf"
               class="bg-secondary text-white px-6 py-3 rounded-xl font-semibold hover:bg-secondary/90 transition-all duration-300 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Télécharger le PDF
            </a>
        </div>

        <!-- PDF Container - Reduced padding -->
        <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- PDF Viewer -->
            <div class="w-full" style="height: 1000px;">
                <iframe 
                    src="{{ asset('documents/LIVRAISONS ET RETOURS ASTROLAB.pdf') }}" 
                    class="w-full h-full"
                    style="border: none;"
                    title="Livraisons et Retours - Astrolab">
                    <div class="flex items-center justify-center h-full p-8">
                        <div class="text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-600 mb-4">Votre navigateur ne supporte pas l'affichage des PDFs.</p>
                            <a href="{{ asset('documents/LIVRAISONS ET RETOURS ASTROLAB.pdf') }}" target="_blank" 
                               class="bg-primary text-white px-6 py-3 rounded-xl font-semibold hover:bg-primary/90 transition-colors inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Ouvrir dans un nouvel onglet
                            </a>
                        </div>
                    </div>
                </iframe>
            </div>
        </div>
        
        <!-- Fallback message -->
        <div class="text-center mt-6 text-white">
            <p>Vous rencontrez des difficultés pour visualiser le document ? 
               <span class="font-semibold">Contactez-nous</span>
            </p>
        </div>

        <!-- Section Contact -->
        <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl shadow-lg p-8 text-white mt-8">
            <div class="text-center">
                <h2 class="text-3xl font-bold mb-4">Une question ?</h2>
                <p class="text-xl mb-6 opacity-90">Notre équipe est là pour vous aider</p>
                                    <div class="flex flex-col md:flex-row gap-4 justify-center items-center">
                        <a href="mailto:contact@astrolab.fr" 
                           style="background-color: white; color: #000000; padding: 12px 32px; border-radius: 12px; font-weight: 600; text-decoration: none; transition: all 0.3s ease;"
                           onmouseover="this.style.backgroundColor='#f3f4f6'"
                           onmouseout="this.style.backgroundColor='white'">
                            contact@astrolab.fr
                        </a>
                        <a href="{{ route('home') }}#contact" class="border-2 border-white text-white px-8 py-3 rounded-xl font-semibold hover:bg-white hover:text-primary transition-colors">
                            Formulaire de contact
                        </a>
                    </div>
            </div>
        </div>
    </div>
</div>
@endsection
