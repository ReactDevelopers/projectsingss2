{{-- FIXME: This has not yet been completed! --}}

<hr class="my-5">

<div class="form-row mb-3">
    <div class="col-sm-6">
        <h5 class="">Races</h5>
    </div>
</div>

<div class="form-row mb-4">
    <div class="col-sm-3">
        <label class="float-left mr-4">Exchange To</label>
        <select class="c-select form-control float-left col-sm-6">
            <option selected>USD</option>
            <option>GBP</option>
            <option>EUR</option>
        </select>
    </div>
</div>

<div class="form-row mb-4">
    <div class="col-sm-12">
        <table class="table table-sm table-striped override-table">
            <thead>
                <tr>
                    <th>Race</th>
                    <th class="text-right">Amount due</th>
                    <th class="text-right">Amount paid</th>
                    <th class="text-right">Balance due</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hotel->races as $race)
                    <tr>
                        <td><a href="{{ route('races.hotels.show', [
                            'race' => $race->id,
                            'hotel' => $hotel->id]
                            ) }}">{{ $race->full_name }}</a></td>
                        <td class="text-right">
                            (coming soon) <br>
                            <small class="form-text text-muted">Exchanged: (coming soon)</small>
                        </td>
                        <td class="text-right">
                            (coming soon)<br>
                            <small class="form-text text-muted">Exchanged: (coming soon)</small>
                        </td>
                        <td class="text-right">
                            (coming soon)<br>
                            <small class="form-text text-muted">Exchanged: (coming soon)</small>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td><strong>Totals</strong></td>
                    <td class="text-right">
                        <strong>(coming soon)</strong> <br>
                        <small class="form-text text-muted">Exchanged: (coming soon)</small>
                    </td>
                    <td class="text-right">
                        <strong>(coming soon)</strong> <br>
                        <small class="form-text text-muted">Exchanged: (coming soon)</small>
                    </td>
                    <td class="text-right">
                        <strong>(coming soon)</strong> <br>
                        <small class="form-text text-muted">Exchanged: (coming soon)</small>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
