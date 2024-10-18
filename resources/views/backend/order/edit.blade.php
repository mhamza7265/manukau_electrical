@extends('backend.layouts.master')

@section('title','Order Detail')

@section('main-content')
<div class="card">
  <h5 class="card-header">Order Edit</h5>
  <div class="card-body">
    <form action="{{route('order.update',$order->id)}}" method="POST">
      @csrf
      @method('PATCH')
      <div class="form-group">
        <label for="status">Status :</label>
        <select name="status" id="" class="form-control">
          <option value="pending" class="{{($order->status=='delivered' || $order->status=="process" || $order->status=="cancelled") ? 'd-none' : ''}}"  {{(($order->status=='pending')? 'selected' : '')}}>Pending</option>
          <option value="processing" class="{{($order->status=='delivered'|| $order->status=="cancelled") ? 'd-none' : ''}}"  {{(($order->status=='processing')? 'selected' : '')}}>Processing</option>
          <option value="shipped" class="{{($order->status=='delivered'|| $order->status=="cancelled") ? 'd-none' : ''}}"  {{(($order->status=='shipped')? 'selected' : '')}}>Shipped</option>
          <option value="delivered" class="{{($order->status=="cancelled") ? 'd-none' : ''}}"  {{(($order->status=='delivered')? 'selected' : '')}}>Delivered</option>
          <option value="cancelled" class="{{($order->status=='delivered') ? 'd-none' : ''}}"  {{(($order->status=='cancelled')? 'selected' : '')}}>Cancelled</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
    .order-info,.shipping-info{
        background:#ECECEC;
        padding:20px;
    }
    .order-info h4,.shipping-info h4{
        text-decoration: underline;
    }

</style>
@endpush
