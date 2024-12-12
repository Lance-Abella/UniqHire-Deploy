<footer>
    <div class="footer-container">
        <div class="socials">
            <div class="border-bottom mb-4">
                <h5 class="mb-4">Socials</h5>
                <ul class="mb-4">
                    <li>
                        <span><i class='bx bxl-facebook-circle'></i> Facebook</span>
                        <a href="">facebook.com</a>
                    </li>
                    <li>
                        <span><i class='bx bxl-instagram-alt'></i> Instagram</span>
                        <a href="">instagram.com</a>
                    </li>
                </ul>
            </div>
            <div>
                <span><i class='bx bx-copyright'></i> 2024 UniqHire | All Rights Reserved</span>

            </div>

        </div>
        <div class="contact">
            <h5 class="mb-4">Send us a message</h5>
            <form action="{{ route('contact-send') }}" method="POST">
                @csrf
                <div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingInput" name="email" required placeholder="Email">
                        <label for="floatingInput">Email</label>
                        @error('email')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" placeholder="Description" id="floatingTextarea2" name="description" style="height: 150px"></textarea>
                        <label for="floatingTextarea2">Description</label>
                        @error('description')
                        <span class="error-msg">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="btn d-flex justify-content-center">
                        <button type="submit" class="border-0 submit-btn">Send</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</footer>