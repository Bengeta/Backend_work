<script type="text/javascript">
    let is_accept = confirm("Give a feedback?");
    if (is_accept) {
        let url = new URL("{{ route('appeal') }}");
        url.searchParams.set('accepted', '1');
        window.location.href = url;
    }
</script>

