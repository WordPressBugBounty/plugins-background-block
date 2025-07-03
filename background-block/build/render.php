<?php
$innerContent = $content;

if( !function_exists( 'evbbGetBoxValues' ) ){
	function evbbGetBoxValues( $val ) {
		return implode( ' ', array_values( $val ) );
	}
}

if( !function_exists( 'borderCSS' ) ){
	function borderCSS($value = ['0px solid #0000']) {
		if (count($value) === 0) {
			return '';
		}
	
		$top = $value[0];
	
		if (count($value) >= 4) {
			$right = $value[1];
			$bottom = $value[2];
			$left = $value[3];
			return "border-top: $top; border-right: $right; border-bottom: $bottom; border-left: $left;";
		}
	
		return "border: $top;";
	}
}

if( !function_exists( 'evbbDeviceCSS' ) ){
	function evbbDeviceCSS( $id, $attributes, $device ) {
		extract( $attributes );

		$wPadding = evbbGetBoxValues( $wrapper['padding'][$device] ?? [] );
		$cMaxW = $content['maxWidth'][$device] ?? '100%';
		$cPadding = evbbGetBoxValues( $content['padding'][$device] ?? [] );
		
		return "
			#$id {
				padding: $wPadding;
			}
			#$id .backgroundContent {
				max-width: $cMaxW;
				padding: $cPadding;
			}
		";
	}
}

if( !function_exists( 'evbbStyle' ) ){
	function evbbStyle( $attributes, $id ) {
		extract( $attributes );
		extract( $background['desktop'] ?? [] );

		$plxSpeed = $wrapper['animation']['parallax']['speed'] ?? 1;

		// Selectors
		$mainSl = "#$id";
		$backgroundSl = "$mainSl .evbBackground";
		$contentWrapperSl = "$mainSl .backgroundContentWrapper";
		$contentSl = "$mainSl .backgroundContent";

		// Image Destructure
		$url = $image['url'] ?? '';
		$position = $image['position'] ?? '';
		$attachment = $image['attachment'] ?? '';
		$repeat = $image['repeat'] ?? '';
		$size = $image['size'] ?? '';

		// Wrapper CSS
		$wMHeight = $wrapper['minHeight'];
		$wrapBorder = borderCSS( $wrapper['border'] ?? [] );
		$wrapRadius = $wrapper['radius'] ?? '0px';
		$wrapShadow = !empty( $wrapper['shadow'] ) ? 'box-shadow: '. $wrapper['shadow'] .';' : '';

		// Content CSS
		$contentBG = $content['background'];
		$cVAlign = $content['align']['vertical'];
		$cHAlign = $content['align']['horizontal'] ?? 'center';
		$cTAlign = $content['align']['text'];
		$cBorder = borderCSS( $content['border'] ?? [] );
		$cRadius = $content['radius'] ?? '0px';
		$cShadow = !empty( $content['shadow'] ) ? 'box-shadow: '. $content['shadow'] .';' : '';

		$bgStyles = 'image' === $type ? "url($url)" :
			('gradient' === $type ? $gradient : $color);

		$bgImgStyles = 'image' === $type ? "
			background-position: $position;
			background-attachment: $attachment;
			background-repeat: $repeat;
			background-size: $size;
		" : '';

		$styles = "$mainSl {
			min-height: $wMHeight;
			$wrapBorder
			border-radius: $wrapRadius;
			$wrapShadow
		}
		$mainSl.scroll-parallax .evbBackground{
			height: calc( 100% + ( 100% * $plxSpeed ) );
		}
		$backgroundSl {
			background: $bgStyles;
			$bgImgStyles
		}

		$contentWrapperSl{
			align-items: $cVAlign;
			justify-content: $cHAlign;
		}
		$contentSl {
			text-align: $cTAlign;
			$contentBG
			$cBorder
			border-radius: $cRadius;
			$cShadow
		}
		". evbbDeviceCSS( $id, $attributes, 'desktop' ) ."

		@media (min-width: 481px) and (max-width: 960px) {". evbbDeviceCSS( $id, $attributes, 'tablet' ) ."}

		@media (max-width: 480px) {". evbbDeviceCSS( $id, $attributes, 'mobile' ) ."}";

		ob_start(); ?>
		<style><?php echo esc_html( wp_strip_all_tags( $styles ) ); ?></style>
		<?php return ob_get_clean();
	}
}

$id = wp_unique_id( 'evbBackground-' );
extract( $attributes );

$aniType = $wrapper['animation']['type'] ?? 'none';

global $allowedposttags;
$commonAttr = [ 'id' => 1, 'class' => 1, 'style' => 1, 'width' => 1, 'height' => 1, 'data-*' => 1, 'aria-label' => 1, 'aria-hidden' => 1 ];
$svgAttr = [ 'fill' => 1, 'stroke' => 1, 'stroke-width' => 1, 'transform' => 1 ] + $commonAttr;

$allowedHTML = wp_parse_args( [
	'style' => [],
	'svg' => $svgAttr + [ 'xmlns' => 1, 'viewbox' => 1 ],
	'circle' => $svgAttr + [ 'cx' => 1, 'cy' => 1, 'r' => 1, 'pathlength' => 1 ],
	'clipPath' => $commonAttr + [ 'clippathunits' => 1 ],
	'desc' => $commonAttr,
	'defs' => $commonAttr,
	'ellipse' => $svgAttr + [ 'cx' => 1, 'cy' => 1, 'rx' => 1, 'ry' => 1 ],
	'g' => $svgAttr,
	'line' => $svgAttr + [ 'x1' => 1, 'x2' => 1, 'y1' => 1, 'y2' => 1 ],
	'linearGradient' => $commonAttr + [ 'gradientUnits' => 1, 'gradientTransform' => 1, 'href' => 1, 'x1' => 1, 'x2' => 1, 'y1' => 1, 'y2' => 1 ],
	'path' => $svgAttr + [ 'd' => 1, 'pathlength' => 1 ],
	'polygon' => $svgAttr + [ 'points' => 1, 'pathlength' => 1 ],
	'polyline' => $svgAttr + [ 'points' => 1, 'pathlength' => 1 ],
	'rect' => $svgAttr + [ 'x' => 1, 'y' => 1, 'rx' => 1, 'ry' => 1, 'pathlength' => 1 ],
	'stop' => $svgAttr + [ 'offset' => 1, 'stop-color' => 1, 'stop-opacity' => 1 ],
	'title' => $commonAttr,
	'iframe' => $commonAttr + [ 'allow' => 1, 'allowfullscreen' => 1, 'loading' => 1, 'name' => 1, 'referrerpolicy' => 1, 'sandbox' => 1, 'src' => 1, 'srcdoc' => 1, 'frameborder' => 1 ]
], $allowedposttags );
?>
<div
	<?php echo get_block_wrapper_attributes([ 'class' => "align$align scroll-$aniType" ]); ?>
	id='<?php echo esc_attr( $id ); ?>'
	data-attributes='<?php echo esc_attr( wp_json_encode( $attributes ) ); ?>'
>
	<div class='evbShape top' data-shape='<?php echo esc_attr( wp_json_encode( $shape['top'] ) ); ?>'></div>

	<?php echo wp_kses( evbbStyle( $attributes, $id ), [ 'style' => [] ] ); ?>

	<div class='evbBackground'></div>

	<div class='backgroundContentWrapper'>
		<div class='backgroundContent is-layout-constrained'>
			<?php echo wp_kses( $innerContent, $allowedHTML ); ?>
		</div>
	</div>

	<div class='evbShape bottom' data-shape='<?php echo esc_attr( wp_json_encode( $shape['bottom'] ) ); ?>'></div>
</div>