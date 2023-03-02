<!DOCTYPE html>
<html>
	<head></head>
	<body>
		
		<div>
			@foreach ($leads as $lead)
				<div>
					<div>
						{{ $lead->id }}
						{{ $lead->name }}
					</div>
					<div>Аккаунт: {{ $lead->account->name }}</div>
					<div>Канал {{ $lead->pipeline->name }}</div>
				</div>
				<br>
			@endforeach
		</div>

		{{ $leads->links() }}

	</body>

</html>