
// === FORMAT NUMBER INPUT ===
function formatNumber(value, justTypedComma = false) {
	value = value.replace(/[^0-9,]/g, '');
	if (!value) return "";
	let parts = value.split(",");
	let integerPart = parts[0] || "0";
	let decimalPart = parts[1] || "";
	let formatted = Number(integerPart).toLocaleString("id-ID");
	if (justTypedComma) return formatted + ",";
	return decimalPart ? formatted + "," + decimalPart.slice(0, 2) : formatted;
}

$(document).on("input", "input[data-format='number']", function(e) {
	let val = $(this).val();
	let lastChar = val.slice(-1);
	let justTypedComma = (lastChar === "," && val.indexOf(",") === val.length - 1);
	$(this).val(formatNumber(val, justTypedComma));
});

$("form").on("submit", function() {
	$(this).find("input[data-format='number']").each(function() {
	let raw = $(this).val().replace(/\./g, '').replace(",", ".");
	$(this).val(raw);
	});
});
