function timeid_to_time(timeID, accuracy) {
	var start = moment.tz([2015, 0, 1], 'America/Phoenix');
	start.add((timeID * accuracy), 'm');
	return start.valueOf();
}