<li class="xoo-aff-fs-display xoo-aff-fsd-{{data.type_data.type}} {{{ ( data.type_data.can_delete !== 'yes'  ) ? 'xoo-aff-locked-field' : '' }}}" id="{{data.field_id}}" data-type="{{data.type_data.type}}">
	<div class="xoo-aff-label xoo-aff-label-{{data.type_data.type}}">
		<span>{{data.type_data.title}}</span>
		<span></span>
	</div>
	<div class="xoo-aff-fsd-cta">
		<# if ( data.type_data.can_delete === "yes" ) { #>
			<span class="xoo-aff-fsd-cta-del fas fa-trash"></span>	
		<# } else { #>
			<span class="fas fa-lock"></span>
        <# } #>
		
	</div>
</li>