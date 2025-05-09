
import React from "react";
import { useContent } from "@/context/ContentContext";
import { Button } from "@/components/ui/button";
import { ScrollArea } from "@/components/ui/scroll-area";
import { Card, CardContent } from "@/components/ui/card";
import { useToast } from "@/components/ui/use-toast";
import { format } from "date-fns";

const ContentManager: React.FC = () => {
  const { contents, deleteContent } = useContent();
  const { toast } = useToast();

  const handleDelete = (id: string) => {
    if (confirm("Are you sure you want to delete this content?")) {
      deleteContent(id);
      toast({ description: "Content deleted successfully" });
    }
  };

  return (
    <Card className="w-full">
      <CardContent className="p-6">
        <h2 className="text-xl font-bold mb-4">Manage Content</h2>
        
        {contents.length === 0 ? (
          <div className="text-center py-8 text-muted-foreground">
            No content has been submitted yet.
          </div>
        ) : (
          <ScrollArea className="h-[400px] w-full pr-4">
            <div className="space-y-4">
              {contents.map((item) => (
                <div
                  key={item.id}
                  className="flex items-start gap-4 p-4 border rounded-lg hover:bg-accent/50 transition-colors"
                >
                  <img
                    src={item.image}
                    alt={item.title}
                    className="w-24 h-16 object-cover rounded flex-shrink-0"
                  />
                  <div className="flex-1 min-w-0">
                    <h3 className="font-medium mb-1">{item.title}</h3>
                    <p className="text-sm text-muted-foreground">
                      {format(item.timestamp, "PPP")}
                    </p>
                    <p className="line-clamp-2 text-sm mt-1">{item.description}</p>
                    <a
                      href={item.link}
                      target="_blank"
                      rel="noopener noreferrer"
                      className="text-primary hover:underline text-sm mt-1 inline-block"
                    >
                      {item.link}
                    </a>
                  </div>
                  <Button
                    variant="destructive"
                    size="sm"
                    onClick={() => handleDelete(item.id)}
                    className="flex-shrink-0"
                  >
                    Delete
                  </Button>
                </div>
              ))}
            </div>
          </ScrollArea>
        )}
      </CardContent>
    </Card>
  );
};

export default ContentManager;
