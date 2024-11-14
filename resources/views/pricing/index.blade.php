@extends('sendportal::layouts.base')

@section('htmlBody')
<div class="container mt-5">
    
    <div class="d-flex justify-content-between mb-4">
        <img src="https://sendportal.io/img/sendportal.png" alt="" style="width: 200px; object-fit:contain">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Logout</button>
        </form>
    </div>

    <h1 class="text-center mb-5">Start Your Subscription</h1>
    
    <div class="d-flex justify-content-center">
        <div class="card shadow-lg p-4" style="width: 30rem; border-radius: 15px;">
            <div class="card-body text-center">
                <h4 class="text-secondary mb-4">$49/month</h4>
                <p class="text-muted">Unlock premium features and priority support.</p>
                <button class="btn btn-primary btn-lg" style="border-radius: 30px;" id="showPaymentForm">Subscribe</button>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Payment Information</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="payment-form" method="POST" action="{{ route('subscribe') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="card-number" class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="card-number" name="card_number" placeholder="4242 4242 4242 4242" maxlength="19" required>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="expiry-date" class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" id="expiry-date" name="expiry_date" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="col">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="password" class="form-control" id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="card-holder" class="form-label">Name on Card</label>
                            <input type="text" class="form-control" id="card-holder" name="card_holder" placeholder="John Doe" required>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg" style="border-radius: 30px;">Confirm Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('showPaymentForm').addEventListener('click', function() {
        var paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    });
</script>

<style>
    body { background-color: #f4f7fa; }
    .card { transition: transform 0.3s, box-shadow 0.3s; }
    .card:hover { transform: translateY(-10px); box-shadow: 0 12px 20px rgba(0, 0, 0, 0.15); }
    input:focus { box-shadow: 0 0 5px rgba(81, 203, 238, 1); border-color: #51cbee; }
    .btn-outline-secondary { border-radius: 20px; }
</style>
@endsection
