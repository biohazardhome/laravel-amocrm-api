<!DOCTYPE html>
<html>
	<head></head>
	<body>
		
		<div>
			@foreach ($leads as $lead)
				<div>
					{{ $lead->id }}
					{{ $lead->name }}
				</div>
			@endforeach
		</div>

		{{ $leads->links() }}

	</body>

</html>