<x-layout>
    @foreach ($teams as $team)
    <div class="container col-6 float-left pr-5 pt-5 pl-5 pb-0">
        <h2 class="text-center p-2 ml-4">{{ $team->team_name }}</h2>
        <table class="table">
            <thead>
            <tr>
                <th class="pl-4">Coach</th>
                <th></th>
                <th></th>
                <th>{{ $team->coach->getFullName() }}</th>
                <th></th>
            </tr>
            <tr>
                <th class="pl-4">Player #</th>
                <th></th>
                <th></th>
                <th>Player Name</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                @foreach($team->players as $player)
                    <tr>
                        <td class="pl-5">{{ $player->id }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $player->getFullName() }}</td>
                        <td></td>
                    </tr>
                @endforeach
                <tr>
                    <th>Total Team Ranking:</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>{{ $team->totalPlayerRanking() }}</th>
                </tr>
            <tr>

            </tr>
            </tbody>
        </table>
    </div>
    @endforeach
{{--    <div class="container-fluid">--}}
{{--        @foreach ($teams as $team)--}}
{{--            <div class="row">--}}
{{--                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">--}}
{{--                    <table class="col-12">--}}
{{--                        <thead class="col-12">--}}
{{--                            <tr class="col-12 text-center">--}}
{{--                                 <th class="offset-10 font-weight-bold">{{ $team->team_name }}</th>--}}
{{--                            </tr>--}}
{{--                            <tr class="col-12">--}}
{{--                                <th class="col-6">Coach</th>--}}
{{--                                <th class="col-6">{{ $team->coach->getFullName() }}</th>--}}
{{--                            </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody>--}}

{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endforeach--}}
{{--    </div>--}}
</x-layout>
