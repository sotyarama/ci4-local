#!/usr/bin/env perl
use strict;
use warnings;
use File::Find;
use File::Spec;

my $root = 'c:/ci4-local';
chdir $root or die "chdir $root: $!";

my @md;
find(sub { push @md, $File::Find::name if -f && /\.md$/i }, '.');

my $broken = 0;
my $trailing = 0;

foreach my $f (@md) {
    open my $fh, '<:encoding(UTF-8)', $f or next;
    my $line_no = 0;
    while (my $line = <$fh>) {
        $line_no++;
        if ($line =~ / $/) {
            print "TRAILING_WS: $f: line $line_no\n";
            $trailing++;
        }
        while ($line =~ /\[([^\]]+)\]\(([^)]+)\)/g) {
            my ($text, $link) = ($1, $2);
            next if $link =~ m{^https?://}i || $link =~ m{^mailto:}i || $link =~ /^#/;
            (my $clean = $link) =~ s/[#?].*$//;
            next if $clean eq '';
            my $target = File::Spec->rel2abs($clean, File::Spec->rel2abs($f));
            if (! -e $target) {
                print "BROKEN_LINK: $f -> [$text]($clean) (resolved: $target)\n";
                $broken++;
            }
        }
    }
    close $fh;
}

print "Checked ", scalar(@md), " markdown files. Trailing whitespace: $trailing. Broken links: $broken.\n";
exit($broken?2:0);
