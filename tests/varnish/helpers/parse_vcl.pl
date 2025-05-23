#!/usr/bin/env perl
use strict;
use warnings;

my $input_file = $ARGV[0] || 'etc/varnish6.vcl';
my $output_file = $ARGV[1] || 'generated.vcl';

open(my $in, '<', $input_file) or die "Can't open $input_file: $!";
open(my $out, '>', $output_file) or die "Can't open $output_file: $!";

my $content = do { local $/; <$in> };

# Handle if-else statements first
while ($content =~ /{{if\s+([^}]+)}}(.*?)(?:{{else}}(.*?))?{{\/if}}/gs) {
    my $condition = uc($1);
    my $if_block = $2;
    my $else_block = $3 // '';
    my $value = $ENV{$condition} || '';
    my $replacement = $value eq '1' ? $if_block : $else_block;
    $content =~ s/{{if\s+([^}]+)}}(.*?)(?:{{else}}(.*?))?{{\/if}}/$replacement/s;
}

# Handle for loops with specific context
$content =~ s/{{for\s+item\s+in\s+([^}]+)}}(.*?){{\/for}}/handle_for($1, $2)/egs;

# Handle remaining variables
while ($content =~ /{{var\s+([^}]+)}}/) {
    my $var_name = uc($1);
    my $value = $ENV{$var_name} || '';
    $content =~ s/{{var\s+$1}}/$value/g;
}

print $out $content;
close($in);
close($out);

sub handle_for {
    my ($list_name, $template) = @_;
    my $list_var = uc($list_name);
    my $items = $ENV{$list_var} || '';
    my $output = '';

    foreach my $item (split(/\s+/, $items)) {
        my $item_content = $template;

        # Keep track of original item name for property lookups
        my $original_item = $item;

        # Handle simple item replacements
        $item_content =~ s/{{var\s+item}}/$item/g;

        # Handle item properties with original item name
        while ($item_content =~ /{{var\s+item\.([^}]+)}}/) {
            my $prop = $1;
            my $prop_var = uc("${original_item}_${prop}");
            my $prop_value = $ENV{$prop_var} || '';
            $item_content =~ s/{{var\s+item\.$prop}}/$prop_value/g;
        }
        $output .= $item_content;
    }
    return $output;
}