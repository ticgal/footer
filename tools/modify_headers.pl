#!/usr/bin/perl

use strict;
use warnings;

use Cwd;
use File::Basename;
use File::Spec;

my $current_directory = Cwd->getcwd();
my $directory_name = basename($current_directory);
my $mode = 0;

if ($directory_name eq "tools") {
    do_dir("..");
} else {
    my $subfolder = File::Spec->catdir($current_directory, "tools");
    if (-d $subfolder) {
        $mode = 1;
        do_dir(".");
    }
}

sub do_dir {
    my ($dir) = @_;
    print "Entering $dir\n";

    my @excluded_dirs = (
        ".git",
        "lib",
        "plugins",
        "vendor",
        "tests",
        "tools",
        "locales",
        "pics"
        );

    opendir my $dh, $dir or die "ERROR: can not read current directory\n";
    foreach my $dir_file (readdir($dh)) {
        if ($dir_file ne '..' && $dir_file ne '.') {
            # Excluded directories
            # if dir exists in excluded directories, skip it
            if (grep { $_ eq $dir_file } @excluded_dirs) {
                print "Skipping $dir/$dir_file\n";
                next;
            }

            if (-d "$dir/$dir_file") {
                do_dir("$dir/$dir_file");
            } else {
                if (!(-l "$dir/$dir_file")) {
                    # Included filetypes - php, css, js => default comment style
                    if (
                        index($dir_file,".php", 0) != -1
                        || index($dir_file,".css", 0) != -1
                        || index($dir_file,".js", 0) != -1
                    ) {
                        do_file("$dir/$dir_file", "", " * ");
                    }
                    # Included filetypes - twig => ({# #})
                    if (index($dir_file,".twig", 0) != -1) {
                        do_file("$dir/$dir_file", "", " # ");
                    }
                    # Included filetypes - sql, sh, pl => Add a specific comment style (#)
                    if (
                        index($dir_file,".sql", 0) != -1
                        || index($dir_file,".sh", 0) != -1
                        || index($dir_file,".pl", 0) != -1
                    ) {
                        do_file("$dir/$dir_file", "", "# ");
                    }
                }
            }
        }
    }
    closedir $dh;
}

sub do_file {
    my ($file, $format, $decor) = @_;
    if ($format ne "") {
        print "Replacing header on $file. (Using specific comment $format)\n";
    } else {
        print "Replacing header on $file.\n";
    }

    ### DELETE HEADERS
    open my $init_fh, '<', $file or die "Could not open $file: $!";
    my @lines = <$init_fh>;
    close $init_fh;

    my $status = '';
    open my $tmp_fh, '>', '/tmp/tmp_glpi.txt' or die "Could not open temporary file: $!";
    foreach my $file_line (@lines) {
        # Did we found header closure tag ?
        if ($file_line =~ m/$format\*\// || $file_line =~ m/$format\#\}/) {
            # if line starts with */ or #} add a space before to fix comment style
            if ($file_line =~ m/$format\*\//) {
                $file_line =~ s/^$format\*\// $format\*\//;
            } elsif ($file_line =~ m/$format\#\}/) {
                $file_line =~ s/^$format\#\}/ $format\#\}/;
            }
            $status = "END";
        }

        # If we have reach the header closure tag, we print the rest of the file
        if ($status =~ m/END/ || $status !~ m/BEGIN/) {
            print $tmp_fh $file_line;
        }

        # If we haven't reach the header closure tag
        if ($status !~ m/END/) {
            # If we found the header open tag...
            if (
                ($file_line =~ m/$format\/\*\*/ || $file_line =~ m/$format\/\*/)
                || $file_line =~ m/$format\{\#/
            ) {
                # if line is /* replace by /**
                if ($file_line =~ m/$format\/\*/) {
                    $file_line =~ s/$format\/\*/$format\*\*/;
                }

                $status = "BEGIN";
                my $header_file = 'TICGAL_HEADER';
                if ($mode == 1) {
                    $header_file = 'tools/TICGAL_HEADER';
                }

                open my $header_fh, '<', $header_file or die "Could not open $header_file: $!";
                my @headers = <$header_fh>;
                foreach my $header_line (@headers) {
                    print $tmp_fh $decor;
                    print $tmp_fh $header_line;
                }
                close $header_fh;
            }
        }
    }
    close($tmp_fh);
    system("cp -f /tmp/tmp_glpi.txt $file");

    # If we haven't found an header on the file, report it
    if ($status eq '') {
        print "Unable to found an header on $file. Please add it manually.\n";
        #exit 1;
    }
}