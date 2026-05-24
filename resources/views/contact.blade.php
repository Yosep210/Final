<x-layouts::home :title="__('Contact')">

    @include('partials.home.heading', [
    'title' => __('Contact'),
    'imgbg' => asset('assets/img/carousel-2.png')
    ])

    {{-- Kontak Info Start --}}
    <div class="py-24">
        <div class="container mx-auto px-4">
            <div class="text-center mx-auto mb-12 max-w-xl">
                <h1 class="text-4xl font-bold mb-4">Kontak Kami</h1>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Alamat --}}
                <div class="flex">
                    <div
                        class="bg-neutral-50 dark:bg-neutral-800 rounded-3xl p-8 text-center w-full border border-neutral-100 dark:border-neutral-700 shadow-sm">
                        <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                            </svg>
                        </div>
                        <h5 class="text-lg font-bold mb-3 text-neutral-800 dark:text-neutral-100">Alamat Kantor</h5>
                        <p class="text-neutral-600 dark:text-neutral-400 leading-relaxed">
                            Green Lake City<br>
                            Rukan Crown Block E no 21<br>
                            Cipondoh - Tangerang
                        </p>
                    </div>
                </div>

                {{-- WhatsApp --}}
                <div class="flex">
                    <div
                        class="bg-neutral-50 dark:bg-neutral-800 rounded-3xl p-8 text-center w-full border border-neutral-100 dark:border-neutral-700 shadow-sm">
                        <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                            </svg>
                        </div>
                        <h5 class="text-lg font-bold mb-3 text-neutral-800 dark:text-neutral-100">WhatsApp</h5>
                        <p class="text-neutral-600 dark:text-neutral-400">+62 8123-4567-890</p>
                    </div>
                </div>

                {{-- Email --}}
                <div class="flex">
                    <div
                        class="bg-neutral-50 dark:bg-neutral-800 rounded-3xl p-8 text-center w-full border border-neutral-100 dark:border-neutral-700 shadow-sm">
                        <div class="w-16 h-16 bg-primary-600 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                            </svg>
                        </div>
                        <h5 class="text-lg font-bold mb-3 text-neutral-800 dark:text-neutral-100">Email</h5>
                        <p class="text-neutral-600 dark:text-neutral-400">admin@jpbuana.com</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
    {{-- Kontak Info End --}}

    {{-- Contact Form + Map Start --}}
    <div class="bg-neutral-50 dark:bg-neutral-900 py-24 overflow-hidden">
        <div class="container mx-auto px-4">
            <div class="flex flex-col lg:flex-row gap-8 items-stretch">

                {{-- Form --}}
                <div class="w-full lg:w-1/2">
                    <div class="bg-white dark:bg-neutral-800 rounded-[2.5rem] p-8 md:p-12 shadow-xl h-full">
                        <p class="text-primary-600 text-lg font-bold mb-1">Kirim Pesan</p>
                        <h1 class="text-3xl font-bold mb-4 text-neutral-800 dark:text-neutral-100">
                            Kami Siap Membantu Anda
                        </h1>
                        <p class="text-neutral-600 dark:text-neutral-400 mb-8 leading-relaxed">
                            Jangan ragu untuk menghubungi kami jika Anda memiliki pertanyaan tentang produk atau
                            peluang bisnis kami. Kami akan segera merespons pesan Anda.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <flux:input wire:model="name" :label="__('Name')" type="text" required
                                    autocomplete="name" />
                            </div>
                            <div>
                                <flux:input wire:model="email" :label="__('Email')" type="email" required
                                    autocomplete="email" />
                            </div>
                            <div class="md:col-span-2">
                                <flux:input wire:model="subject" :label="__('Subject')" type="text"
                                    autocomplete="off" />
                            </div>
                            <div class="md:col-span-2">
                                <flux:textarea wire:model="pesan" :label="__('Pesan')"
                                    placeholder="Tulis pesan Anda di sini" rows="5" />
                            </div>
                            <div class="md:col-span-2">
                                <flux:button variant="primary" type="submit"
                                    class="w-full md:w-auto rounded-full px-12 py-4 shadow-lg transition-transform hover:scale-105 active:scale-95">
                                    Kirim Pesan
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Map --}}
                <div class="w-full lg:w-1/2 min-h-[450px]">
                    <iframe class="w-full h-full min-h-[450px] rounded-[2.5rem] shadow-2xl border-0"
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.591424568914!2d106.70130067489937!3d-6.185396860604089!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f900297dcee5%3A0xb1775a08592146f3!2sPT.%20Jagad%20Pesona%20Buana!5e0!3m2!1sid!2sid!4v1772474918787!5m2!1sid!2sid"
                        allowfullscreen="" aria-hidden="false" tabindex="0" loading="lazy"></iframe>
                </div>

            </div>
        </div>
    </div>
    {{-- Contact Form + Map End --}}

</x-layouts::home>