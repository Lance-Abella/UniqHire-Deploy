@extends('layout')

@section('page-title', 'History of Transactions')
@section('page-content')
<div class="users-container">
    <div class="row mt-4 mb-2">
        <div class="text-start header-texts back-link-container border-bottom">
            History of Transactions.
        </div>
    </div>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <td class="table-head">Transaction No.</td>
                <td class="table-head">Date</td>
                <!-- <td class="table-head">Time</td> -->
                <td class="table-head">Name Used</td>
                <td class="table-head">Email</td>
                <td class="table-head">Program Name</td>
                <td class="table-head">Receiver</td>
                <td class="table-head">Amount Sent</td>
            </tr>
        </thead>
        <tbody class="table-group-divider text-center">
            @forelse ($transactions as $transaction)
            <tr>
                <td>{{ $transaction->transaction_id }}</td>
                <td>{{ $transaction->created_at->format('F d, Y') }}</td>
                <!-- <td>{{ $transaction->created_at->format('h:i A') }}</td> -->
                <td>{{ $transaction->name }}</td>
                <td>{{ $transaction->email }}</td>
                <td>{{ $transaction->crowdfundEvent->program->title }}</td>
                <td>{{ $transaction->receiver }}</td>
                <td>{{ 'PHP ' . number_format($transaction->amount, 0, '.', ',') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No Transactions yet.</td>
            </tr>
            @endforelse
            <tr>
                <td colspan="6" class="text-end table-head"> Total:</td>
                <td style="background-color: var(--primary-color-light); color: var(--primary-color); font-weight: var(--header-extrabold)">{{'PHP ' . number_format($totalAmount, 0, '.', ',')}}</td>
            </tr>
        </tbody>
    </table>
    <div class="pagination-container">
        <div class="pagination">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
<script>
    function confirmDelete(event, formId) {
        event.preventDefault();
        Swal.fire({
            title: "Confirmation",
            text: "Do you really want to delete this?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Confirm"
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }
</script>
@endsection