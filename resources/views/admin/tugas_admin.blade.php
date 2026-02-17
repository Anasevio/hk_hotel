<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Form</title>
    <link rel="stylesheet" href="{{ asset('css/tugas_admin.css') }}">
</head>
<body>
    <div class="container">

        <!-- Main Card -->
        <div class="card">
            <!-- Header -->
            <div class="header">
                <h1>Tugas</h1>
            </div>

            <!-- Form -->
            <div class="form-container">

                @if(session('status'))
                    <div class="success-message show" style="margin-bottom:12px">{{ session('status') }}</div>
                @endif

                @if($errors->any())
                    <div style="margin-bottom:12px; color:#b91c1c; font-weight:600">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form id="taskForm" method="POST" action="{{ route('admin.tugas.store') }}">
                    @csrf
                    <!-- Room Selection -->
                    <div class="form-group">
                        <label for="room" class="form-label">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pilih Ruangan
                        </label>
                        <select id="room" name="room" class="form-control" required>
                            <option value="">Pilih ruangan...</option>
                            <option value="Ruang Meeting A">Ruang Meeting A</option>
                            <option value="Ruang Meeting B">Ruang Meeting B</option>
                            <option value="Ruang Kelas 101">Ruang Kelas 101</option>
                            <option value="Ruang Kelas 102">Ruang Kelas 102</option>
                            <option value="Ruang Laboratorium">Ruang Laboratorium</option>
                            <option value="Ruang Perpustakaan">Ruang Perpustakaan</option>
                            <option value="Auditorium">Auditorium</option>
                        </select>
                    </div>

                    <!-- Time Selection -->
                    <div class="form-group">
                        <label for="time" class="form-label">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Waktu
                        </label>
                        <select id="time" name="time" class="form-control" required>
                            <option value="">Pilih waktu...</option>
                            <option value="01:00">01:00</option>
                            <option value="02:00">02:00</option>
                            <option value="03:00">03:00</option>
                            <option value="04:00">04:00</option>
                            <option value="05:00">05:00</option>
                            <option value="06:00">06:00</option>
                            <option value="07:00">07:00</option>
                            <option value="08:00">08:00</option>
                            <option value="09:00">09:00</option>
                            <option value="10:00">10:00</option>
                            <option value="11:00">11:00</option>
                            <option value="12:00">12:00</option>
                            <option value="13:00">13:00</option>
                            <option value="14:00">14:00</option>
                            <option value="15:00">15:00</option>
                            <option value="16:00">16:00</option>
                            <option value="17:00">17:00</option>
                            <option value="18:00">18:00</option>
                            <option value="19:00">19:00</option>
                            <option value="20:00">20:00</option>
                            <option value="21:00">21:00</option>
                            <option value="22:00">22:00</option>
                            <option value="23:00">23:00</option>
                            <option value="00:00">00:00</option>
                        </select>
                    </div>

                    <!-- Message Area -->
                    <div class="form-group">
                        <label for="message" class="form-label">
                            <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            Pesan Tugas
                        </label>
                        <textarea id="message" name="message" class="form-control" placeholder="Masukkan detail tugas Anda di sini..." required>{{ old('message') }}</textarea>
                        <div class="char-counter">
                            <span id="charCount">0</span> karakter
                        </div>
                    </div>

                    <!-- hidden selected user id (if any) -->
                    @if(!empty($selectedUser))
                        <input type="hidden" name="user_id" value="{{ $selectedUser->id }}" />
                        <div style="margin-bottom:12px; color:#374151; font-weight:600">Untuk: {{ $selectedUser->name }}</div>
                    @endif

                    <!-- Submit Button -->
                    <button type="submit" class="submit-btn" id="submitBtn">
                        <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span id="btnText">Kirim Tugas</span>
                    </button>

                    <!-- Success Message -->
                    <div class="success-message" id="successMessage">
                        <div class="success-icon">
                            <svg style="width: 16px; height: 16px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="success-text">Tugas berhasil dikirim!</p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Sistem Manajemen Tugas - <span id="year"></span></p>
        </div>
    </div>

    <script>
        // Character counter
        const messageInput = document.getElementById('message');
        const charCount = document.getElementById('charCount');

        messageInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Form submission
        const form = document.getElementById('taskForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const successMessage = document.getElementById('successMessage');

        form.addEventListener('submit', function(e) {
            // simple client-side UX: disable submit to prevent double submissions, then allow normal POST
            submitBtn.disabled = true;
            btnText.textContent = 'Mengirim...';
            submitBtn.querySelector('.icon')?.remove();
            submitBtn.insertAdjacentHTML('afterbegin', '<div class="spinner"></div>');
            // let the browser submit the form (no e.preventDefault)
        });

        // restore old values (after validation error)
        @if(old('room'))
            try { document.getElementById('room').value = {!! json_encode(old('room')) !!}; } catch(e){}
        @endif
        @if(old('time'))
            try { document.getElementById('time').value = {!! json_encode(old('time')) !!}; } catch(e){}
        @endif
        @if(old('message'))
            try { document.getElementById('message').value = {!! json_encode(old('message')) !!}; charCount.textContent = {!! json_encode(strlen(old('message'))) !!}; } catch(e){}
        @endif

        // Set current year
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>