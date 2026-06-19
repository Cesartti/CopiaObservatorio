class CustomColors extends AbstractColors{
	static main='#7d2d91';
	static mid='#a855f7';
	static light='#fce7f3';
	static opposite='#1f6b45';
	static midOpposite=super.midAmbi;
	static hardOpposite=super.mainAmbi;
	static palette=[
		'#7d2d91',  // púrpura principal del observatorio
		'#ef6f8f',  // rosa accent
		'#a855f7',  // púrpura claro
		'#ec4899',  // rosa vivo
		'#d946ef',  // fucsia
		'#f43f5e',  // rosa-rojo
		'#be185d',  // magenta oscuro
		'#6366f1',  // índigo
		'#f59e0b',  // ámbar (contraste)
		super.midGray,
		super.cyan];
}
